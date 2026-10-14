<?php
declare(strict_types = 1);

namespace PHPUnit\Framework\Attributes {

	if (PHP_VERSION_ID >= 80000 && (int)explode('.', \PHPUnit\Runner\Version::series())[0] < 10) {

		#[\Attribute]
		final class DataProvider
		{

			public function __construct(string $methodName)
			{
			}

		}


		#[\Attribute]
		final class RequiresPhp
		{

			public function __construct(string $versionRequirement)
			{
			}

		}

	}

}
