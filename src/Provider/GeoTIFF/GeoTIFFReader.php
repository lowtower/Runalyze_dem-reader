<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Provider\GeoTIFF;

use Runalyze\DEM\Exception\RuntimeException;
use Runalyze\DEM\Provider\AbstractResourceReader;

/**
 * @see http://www.awaresystems.be/imaging/tiff/tifftags.html
 */
class GeoTIFFReader extends AbstractResourceReader
{
    /**
     * The number of bytes required to hold a TIFF offset address.
     */
    public const int LEN_OFFSET = 4;

    /**
     * Magic number located at bytes 2-3 which identifies a TIFF file.
     */
    public const int MAGIC_TIFF_ID = 42;

    public const int TIFF_CONST_STRIPOFSETS = 273;

    public const int TIFF_CONST_IMAGE_WIDTH = 256;

    public const int TIFF_CONST_IMAGE_LENGTH = 257;

    public const int TIFF_CONST_BITS_PER_SAMPLE = 258;

    public const int TIFF_CONST_SAMPLES_PER_PIXEL = 277;

    public const int TIFF_CONST_ROWS_PER_STRIP = 278;

    public const int TIFF_CONST_STRIPBYTECOUNTS = 279;

    public const int TIFF_CONST_IFD_ENTRY_BYTES = 12;

    public const string BIG_ENDIAN = 'MM';

    public const string LITTLE_ENDIAN = 'II';

    public const int UNKNOWN = -32768;

    /**
     * @var int The number of bytes containing each item of elevation data
     */
    protected int $BytesPerSample = 2;

    /**
     * @var int 	The number of components per pixel
     */
    protected int $SamplesPerPixel = 1;

    /**
     * @var int The number of rows per strip
     */
    protected int $RowsPerStrip = 1;

    protected int $NumDataRows = 0;

    protected int $NumDataCols = 0;

    protected int $StripOffsets = 0;

    protected string $ByteOrder = '';

    public function readHeader(): void
    {
        $this->checkByteOrderAndTiffIdentifier();
        $this->goToIFDEntries();
        $this->readIFDEntries();
    }

    /**
     * Go to the file header and work out the byte order (bytes 0-1) and TIFF identifier (bytes 2-3).
     * @throws \Runalyze\DEM\Exception\RuntimeException
     */
    protected function checkByteOrderAndTiffIdentifier(): void
    {
        if (! is_resource($this->FileResource)) {
            throw new RuntimeException('File resource is not valid.');
        }

        fseek($this->FileResource, 0);
        $data = unpack('c2chars/vTIFF_ID', fread($this->FileResource, max(1, 4)) ?: '');

        if ($data === false || ! isset($data['TIFF_ID'])) {
            throw new RuntimeException('Provided GeoTIFF file is not a valid tiff file.');
        }

        if ($data['TIFF_ID'] !== self::MAGIC_TIFF_ID) {
            throw new RuntimeException('Provided GeoTIFF file is not a valid tiff file.');
        }

        $this->ByteOrder = sprintf('%c%c', $data['chars1'], $data['chars2']);

        if ($this->ByteOrder !== self::LITTLE_ENDIAN && $this->ByteOrder !== self::BIG_ENDIAN) {
            throw new RuntimeException('Provided GeoTIFF file has an unknown byte order.');
        }
    }

    /**
     * The remaining 4 bytes in the header are the offset to the IFD.
     */
    protected function goToIFDEntries(): void
    {
        if (! is_resource($this->FileResource)) {
            throw new RuntimeException('File resource is not valid.');
        }

        fseek($this->FileResource, 4);

        $ifdOffsetFormat = $this->isLittleEndian() ? 'VIFDoffset' : 'NIFDoffset';
        $data = unpack($ifdOffsetFormat, fread($this->FileResource, max(1, 4)) ?: '');

        if ($data === false || ! isset($data['IFDoffset'])) {
            throw new RuntimeException('Could not read IFD offset from GeoTIFF file.');
        }

        fseek($this->FileResource, (int) $data['IFDoffset']);
    }

    /**
     * Read IFD entries which (the number of entries in each is in the first two bytes).
     */
    protected function readIFDEntries(): void
    {
        if (! is_resource($this->FileResource)) {
            throw new RuntimeException('File resource is not valid.');
        }

        $countFormat = $this->isLittleEndian() ? 'vcount' : 'ncount';
        $countData = unpack($countFormat, fread($this->FileResource, max(1, 2)) ?: '');

        if ($countData === false || ! isset($countData['count'])) {
            throw new RuntimeException('Could not read IFD entry count from GeoTIFF file.');
        }

        $ifdFormat = $this->isLittleEndian() ? 'vtag/vtype/Vcount/Voffset' : 'ntag/ntype/Ncount/Noffset';

        for ($i = 0; $i < $countData['count']; ++$i) {
            $constData = unpack($ifdFormat, fread($this->FileResource, max(1, self::TIFF_CONST_IFD_ENTRY_BYTES)) ?: '');

            if ($constData === false || ! isset($constData['tag'])) {
                continue;
            }

            switch ($constData['tag']) {
                case self::TIFF_CONST_IMAGE_WIDTH:
                    $this->NumDataCols = $constData['offset'];
                    break;
                case self::TIFF_CONST_IMAGE_LENGTH:
                    $this->NumDataRows = $constData['offset'];
                    break;
                case self::TIFF_CONST_BITS_PER_SAMPLE:
                    $this->BytesPerSample = (int) ($constData['offset'] / 8);
                    break;
                case self::TIFF_CONST_SAMPLES_PER_PIXEL:
                    $this->SamplesPerPixel = $constData['offset'];
                    break;
                case self::TIFF_CONST_ROWS_PER_STRIP:
                    $this->RowsPerStrip = $constData['offset'];
                    break;
                case self::TIFF_CONST_STRIPOFSETS:
                    $this->StripOffsets = $constData['offset'];
                    break;
            }
        }
    }

    protected function isLittleEndian(): bool
    {
        return $this->ByteOrder === self::LITTLE_ENDIAN;
    }

    /**
     * @return array{0:float,1:float} array(row, col)
     */
    public function getExactRowAndColFor(float $relativeLatitude, float $relativeLongitude): array
    {
        return [
            $relativeLatitude * ($this->NumDataRows - 1),
            $relativeLongitude * ($this->NumDataCols - 1),
        ];
    }

    public function getElevationFor(int $row, int $col): int|bool
    {
        if (! is_resource($this->FileResource)) {
            return false;
        }

        fseek($this->FileResource, $this->StripOffsets + (int) ceil($row / $this->RowsPerStrip) * self::LEN_OFFSET);

        $firstColumnData = unpack('Voffset', fread($this->FileResource, max(1, self::LEN_OFFSET)) ?: '');

        if ($firstColumnData === false || ! isset($firstColumnData['offset'])) {
            return false;
        }

        fseek($this->FileResource, (int) $firstColumnData['offset'] + ($this->NumDataCols * ($row % $this->RowsPerStrip) + $col) * $this->BytesPerSample);

        $elevationData = unpack('velevation', fread($this->FileResource, max(1, $this->BytesPerSample)) ?: '');

        if ($elevationData === false || ! isset($elevationData['elevation'])) {
            return false;
        }

        $elevation = $elevationData['elevation'];

        return ($elevation <= self::UNKNOWN || $elevation >= -self::UNKNOWN) ? false : $elevation;
    }
}
