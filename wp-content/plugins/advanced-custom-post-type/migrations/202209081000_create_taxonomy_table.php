<?php

use ACPT\Includes\ACPT_DB;
use ACPT\Includes\ACPT_Schema_Migration;

class CreateTaxonomyTable extends ACPT_Schema_Migration
{
	/**
	 * @return array
	 */
	public function up(): array
	{
		return [
			"CREATE TABLE IF NOT EXISTS `".ACPT_DB::TABLE_TAXONOMY."` (
	            id VARCHAR(36) UNIQUE NOT NULL,
	            slug VARCHAR(32) UNIQUE NOT NULL,
	            singular VARCHAR(255) NOT NULL,
	            plural VARCHAR(255) NOT NULL,
	            labels TEXT,
	            settings TEXT,
	            PRIMARY KEY(id)
	        ) ".ACPT_DB::getCharsetCollation().";",
			"CREATE TABLE IF NOT EXISTS `".ACPT_DB::TABLE_TAXONOMY_PIVOT."` (
	            custom_post_type_id VARCHAR(36) NOT NULL,
	            taxonomy_id VARCHAR(36) NOT NULL,
	            PRIMARY KEY( `custom_post_type_id`, `taxonomy_id`)
	        ) ".ACPT_DB::getCharsetCollation().";",
			$this->renameTableQuery(ACPT_DB::TABLE_TAXONOMY),
			$this->renameTableQuery(ACPT_DB::TABLE_TAXONOMY_PIVOT),
		];
	}

	/**
	 * @return array
	 */
	public function down(): array
	{
		return [
			$this->deleteTableQuery(ACPT_DB::TABLE_TAXONOMY),
			$this->deleteTableQuery(ACPT_DB::prefixedTableName(ACPT_DB::TABLE_TAXONOMY)),
			$this->deleteTableQuery(ACPT_DB::TABLE_TAXONOMY_PIVOT),
			$this->deleteTableQuery(ACPT_DB::prefixedTableName(ACPT_DB::TABLE_TAXONOMY_PIVOT)),
		];
	}

	/**
	 * @inheritDoc
	 */
	public function version(): string
	{
		return '1.0.197';
	}
}