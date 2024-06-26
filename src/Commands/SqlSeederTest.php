<?php declare(strict_types = 1);

namespace LastDragon_ru\LaraASP\Migrator\Commands;

use LastDragon_ru\LaraASP\Migrator\Package;
use LastDragon_ru\LaraASP\Migrator\Testing\Package\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
#[CoversClass(SqlSeeder::class)]
final class SqlSeederTest extends TestCase {
    public function testHandle(): void {
        // Pre test
        $pkg    = Package::Name;
        $path   = self::getTempDirectory();
        $finder = Finder::create()->in($path);

        self::assertCount(0, $finder->files());

        // Redefine path where files will be generated.
        self::assertNotNull($this->app);

        $this->app->useDatabasePath($path);

        // Call
        $this->artisan("{$pkg}:sql-seeder", [
            'name' => 'SqlSeeder',
        ]);

        // Test
        $expected = [
            'SqlSeeder.php',
            'SqlSeeder.sql',
        ];
        $actual   = [];

        foreach ($finder->files()->sortByName() as $file) {
            $actual[] = $file->getFilename();
        }

        self::assertEquals($expected, $actual);
    }
}
