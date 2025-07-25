<?php
/**
 * Filter class file
 *
 * @package Mantle
 */

namespace Mantle\Support\Attributes;

use Attribute;

/**
 * Hook Filter Attribute
 *
 * Used to hook a method to an WordPress hook at a specific priority.
 */
#[Attribute( Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION )]
class Filter {
	/**
	 * Constructor.
	 *
	 * @param string $hook_name Hook name.
	 * @param int    $priority Priority, defaults to 10.
	 */
	public function __construct( public readonly string $hook_name, public readonly int $priority = 10 ) {}
}
