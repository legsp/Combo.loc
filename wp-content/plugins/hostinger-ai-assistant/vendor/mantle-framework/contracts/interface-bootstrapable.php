<?php
/**
 * Bootstrapable interface file.
 *
 * @package Mantle
 */

namespace Mantle\Contracts;

use Mantle\Application\Application;

/**
 * Bootstrapable Contract
 */
interface Bootstrapable {
	/**
	 * Bootstrap method.
	 *
	 * @param Application $app Application instance.
	 * @param Kernel|null $kernel Kernel instance.
	 */
	public function bootstrap( Application $app, ?Kernel $kernel ): void;
}
