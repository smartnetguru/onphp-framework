<?php
/***************************************************************************
 *   Copyright (C) 2007 by Dmitry E. Demidov                               *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 ***************************************************************************/
/* $Id$ */

	/**
	 * @ingroup Utils
	**/
	final class ClassUtils extends StaticFactory
	{
		/* void */ public static function copyProperties($source, $destination)
		{
			Assert::isTrue(get_class($source) == get_class($destination));
			
			$class = new ReflectionClass($source);
			
			foreach ($class->getProperties() as $property) {
				$name = ucfirst($property->getName());
				$getter = 'get'.$name;
				$setter = 'set'.$name;
				
				if (
					($class->hasMethod($getter))
					&& ($class->hasMethod($setter))
				) {
					
					$sourceValue = $source->$getter();
					
					if ($sourceValue === null) {
						
						$setMethood = $class->getMethod($setter);
						$parameterList = $setMethood->getParameters();
						$firstParameter = $parameterList[0];
						
						if ($firstParameter->allowsNull())
							$destination->$setter($sourceValue);
						
					} else {
						$destination->$setter($sourceValue);
					}
				}
			}
		}
		
		/* void */ public static function copyNotNullProperties($source, $destination)
		{
			Assert::isTrue(get_class($source) == get_class($destination));
			
			$class = new ReflectionClass($source);
			
			foreach ($class->getProperties() as $property) {
				$name = ucfirst($property->getName());
				$getter = 'get'.$name;
				$setter = 'set'.$name;
				
				if (
					($class->hasMethod($getter))
					&& ($class->hasMethod($setter))
				) {
					$value = $source->$getter();
					if ($value !== null)
						$destination->$setter($value);
				}
			}
		}
		
		/* void */ public static function fillNullProperties($source, $destination)
		{
			Assert::isTrue(get_class($source) == get_class($destination));
			
			$class = new ReflectionClass($source);
			
			foreach ($class->getProperties() as $property) {
				$name = ucfirst($property->getName());
				$getter = 'get'.$name;
				$setter = 'set'.$name;
				
				if (
					($class->hasMethod($getter))
					&& ($class->hasMethod($setter))
				) {
					$destinationValue = $destination->$getter();
					$sourceValue = $source->$getter();
					
					if (
						($destinationValue === null)
						&& ($sourceValue !== null) 
					) {
						$destination->$setter($sourceValue);
					}
				}
			}
		}
		
		public static function isInstanceOf($object, $class)
		{
			if (is_object($class)) {
				$className = get_class($class);
			} elseif (is_string($class)) {
				$className = $class;
			} else {
				throw new WrongArgumentException('strange class given');
			}
			
			if (is_string($object))
				$object = new $object;
			
			Assert::isTrue(class_exists($className, true));
			
			$info = new ReflectionClass($className);
			
			return (
				is_subclass_of($object, $className)
				|| $info->isInstance($object)
			);
		}
	}
?>