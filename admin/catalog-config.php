<?php
	const CATALOG_TABLES = [
	    'product_categories'       => 'Product Categories',
	    'product'                  => 'Product',
	    'attributes'               => 'Attributes',
	    'attribute_values'         => 'Attribute Values',
	    'product_variants'         => 'Product Variants',
	    'variant_attribute_values' => 'Variant Attribute Values',
	    'product_images'           => 'Product Images'
	];

	/**
	 * Validate catalog table name.
	 */
	function catalogTable($name) {
	    if (!isset(CATALOG_TABLES[$name])) {
	        throw new RuntimeException('Invalid module');
	    }

	    return $name;
	}

	/**
	 * Get all columns from the specified table.
	 */
	function catalogColumns($conn, $table) {
	    $stmt = $conn->query(
	        'SHOW FULL COLUMNS FROM `' . $table . '`'
	    );

	    return $stmt->fetch_all(MYSQLI_ASSOC);
	}

	/**
	 * Get foreign key relationships for the specified table.
	 */
	function catalogForeignKeys($conn, $table) {
	    $stmt = $conn->prepare(
	        'SELECT
	            COLUMN_NAME,
	            REFERENCED_TABLE_NAME,
	            REFERENCED_COLUMN_NAME
	        FROM
	            information_schema.KEY_COLUMN_USAGE
	        WHERE
	            TABLE_SCHEMA = DATABASE()
	            AND TABLE_NAME = ?
	            AND REFERENCED_TABLE_NAME IS NOT NULL'
	    );

	    $stmt->bind_param('s', $table);
	    $stmt->execute();

	    $out = [];

	    foreach ($stmt->get_result() as $row) {
	        $out[$row['COLUMN_NAME']] = $row;
	    }

	    return $out;
	}

	/**
	 * Check whether a column represents an image.
	 */
	function catalogImageColumn($name) {
	    return (bool) preg_match(
	        '/(^image$|image_url|image_path|image_file|file_path|filename|image_name|\bphoto\b)/i',
	        $name
	    );
	}

	/**
	 * Check whether a column is editable.
	 */
	function catalogEditable($col) {
		
	    $name = $col['Field'];

	    return $col['Extra'] !== 'auto_increment'
	        && !preg_match(
	            '/^(created_at|updated_at|created_by|updated_by)$/',
	            $name
	        )
	        && stripos($col['Extra'], 'GENERATED') === false;
	}