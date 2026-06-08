<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vendors')) {
            if (Schema::hasColumn('vendors', 'phone')) {
                DB::statement('ALTER TABLE `vendors` CHANGE `phone` `phoneNumber` VARCHAR(255) NOT NULL');
            }

            if (Schema::hasColumn('vendors', 'rating_avg')) {
                DB::statement('ALTER TABLE `vendors` CHANGE `rating_avg` `rating` DECIMAL(3,2) NOT NULL DEFAULT 0.00');
            }

            if (Schema::hasColumn('vendors', 'email')) {
                DB::statement('ALTER TABLE `vendors` MODIFY COLUMN `email` VARCHAR(255) NULL');
            }

            if (Schema::hasColumn('vendors', 'location') && !Schema::hasColumn('vendors', 'locations')) {
                DB::statement('ALTER TABLE `vendors` ADD COLUMN `locations` JSON NULL AFTER `description`');
                DB::statement('UPDATE `vendors` SET `locations` = JSON_ARRAY(`location`) WHERE `location` IS NOT NULL');
                DB::statement('ALTER TABLE `vendors` DROP COLUMN `location`');
            }

            if (!Schema::hasColumn('vendors', 'imageIcon')) {
                DB::statement('ALTER TABLE `vendors` ADD COLUMN `imageIcon` VARCHAR(255) NULL AFTER `rating`');
            }

            if (!Schema::hasColumn('vendors', 'instagram')) {
                DB::statement('ALTER TABLE `vendors` ADD COLUMN `instagram` VARCHAR(255) NULL AFTER `locations`');
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vendors')) {
            if (Schema::hasColumn('vendors', 'phoneNumber')) {
                DB::statement('ALTER TABLE `vendors` CHANGE `phoneNumber` `phone` VARCHAR(255) NOT NULL');
            }

            if (Schema::hasColumn('vendors', 'rating')) {
                DB::statement('ALTER TABLE `vendors` CHANGE `rating` `rating_avg` DECIMAL(3,2) NOT NULL DEFAULT 0.00');
            }

            if (Schema::hasColumn('vendors', 'locations') && !Schema::hasColumn('vendors', 'location')) {
                DB::statement('ALTER TABLE `vendors` ADD COLUMN `location` VARCHAR(255) NULL AFTER `description`');
                DB::statement('UPDATE `vendors` SET `location` = JSON_UNQUOTE(JSON_EXTRACT(`locations`, "$[0]")) WHERE `locations` IS NOT NULL');
                DB::statement('ALTER TABLE `vendors` DROP COLUMN `locations`');
            }

            if (Schema::hasColumn('vendors', 'imageIcon')) {
                DB::statement('ALTER TABLE `vendors` DROP COLUMN `imageIcon`');
            }

            if (Schema::hasColumn('vendors', 'instagram')) {
                DB::statement('ALTER TABLE `vendors` DROP COLUMN `instagram`');
            }
        }
    }
};