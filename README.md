# DEM reader

[![Latest Stable Version](https://img.shields.io/packagist/v/runalyze/dem-reader.svg)](https://packagist.org/packages/runalyze/dem-reader)
[![CI](https://github.com/lowtower/Runalyze_dem-reader/actions/workflows/ci.yml/badge.svg)](https://github.com/lowtower/Runalyze_dem-reader/actions/workflows/ci.yml)
[![Code Coverage](https://img.shields.io/badge/coverage-See%20Report-brightgreen)](https://lowtower.github.io/Runalyze_dem-reader/)
[![MIT License](https://img.shields.io/github/license/twbs/bootlint.svg)](https://github.com/Runalyze/dem-reader/blob/master/LICENSE)

Library to read digital elevation models, such as GeoTIFF files for SRTM.
The GeoTIFF reader itself is originally based on [Bob Osola's SRTMGeoTIFFReader](http://www.osola.org.uk/elevations/index.htm).

## Usage

```php
use Runalyze\DEM\Reader;
use Runalyze\DEM\Provider\GeoTIFF\SRTM4Provider;

$Provider = new SRTM4Provider('path/to/srtm/files');
$Reader = new Reader($Provider);
$elevations = $Reader->getElevations($latitudes, $longitudes);
```

To give you some more details: Each provider is valid for its own. You can just ignore the general reader and
use your favorite provider:

```php
use Runalyze\DEM\Provider\GeoTIFF\SRTM4Provider;

$Provider = new SRTM4Provider('path/to/srtm/files');
$elevations = $Provider->getElevations($latitudes, $longitudes);
```

But you may have more than one provider available or different paths where your dem files are located.
You can attach as many providers as you want to the reader. Each of them will be checked if it can handle
the given elevation data (in the order they were attached) and the wirst one that does will be used.

```php
use Runalyze\DEM\Reader;
use Runalyze\DEM\Provider\GeoTIFF\SRTM4Provider;

$Reader = new Reader();
$Reader->addProvider(new SRTM4Provider('path/to/europe/srtm/files'));
$Reader->addProvider(new SRTM4Provider('path/to/america/srtm/files'));

$europeElevations = $Reader->getElevations($europeLatitudes, $europeLongitudes);
$americaElevations = $Reader->getElevations($americaLatitudes, $americaLongitudes);
```

## License

Code released under [the MIT license](LICENSE).
