<?php
/***************************************************************************
 *   Copyright (C) 2006-2007 by Konstantin V. Arkhipov                     *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 ***************************************************************************/
/* $Id$ */

	/**
	 * Generic SQL data types.
	 * 
	 * @ingroup OSQL
	**/
	final class DataType extends Enumeration implements DialectString
	{
		const SMALLINT			= 0x000001;
		const INTEGER			= 0x000002;
		const BIGINT			= 0x000003;
		const NUMERIC			= 0x011004;
		
		const REAL				= 0x000005;
		const DOUBLE			= 0x000006;
		
		const BOOLEAN			= 0x000007;
		
		const CHAR				= 0x000108;
		const VARCHAR			= 0x000109;
		const TEXT				= 0x00000A;
		
		const FULLTEXT			= 0x00000B;
		
		const DATE				= 0x00000C;
		const TIME				= 0x10100D;
		const TIMESTAMP			= 0x10100E;
		
		const HAVE_SIZE			= 0x000100;
		const HAVE_PRECISION	= 0x001000;
		const HAVE_SCALE		= 0x010000;
		const HAVE_TIMEZONE		= 0x100000;
		
		private $size		= null;
		private $precision	= null;
		private $scale		= null;
		
		private $null		= true;
		private $timezone	= false;
		
		protected $names = array(
			self::SMALLINT		=> 'SMALLINT',
			self::INTEGER		=> 'INTEGER',
			self::BIGINT		=> 'BIGINT',
			self::NUMERIC		=> 'NUMERIC',
			
			self::REAL			=> 'REAL',
			self::DOUBLE		=> 'DOUBLE PRECISION',
			
			self::BOOLEAN		=> 'BOOLEAN',
			
			self::CHAR			=> 'CHARACTER',
			self::VARCHAR		=> 'CHARACTER VARYING',
			self::TEXT			=> 'TEXT',
			
			self::FULLTEXT		=> 'FULLTEXT',
			
			self::DATE			=> 'DATE',
			self::TIME			=> 'TIME',
			self::TIMESTAMP		=> 'TIMESTAMP'
		);
		
		/**
		 * @return DataType
		**/
		public static function create($id)
		{
			return new self($id);
		}
		
		public function getSize()
		{
			return $this->size;
		}
		
		/**
		 * @throws WrongArgumentException
		 * @return DataType
		**/
		public function setSize($size)
		{
			Assert::isInteger($size);
			Assert::isTrue(($this->id & self::HAVE_SIZE) > 0);
			
			$this->size = $size;
			
			return $this;
		}
		
		public function getPrecision()
		{
			return $this->precision;
		}
		
		/**
		 * @throws WrongArgumentException
		 * @return DataType
		**/
		public function setPrecision($precision)
		{
			Assert::isInteger($precision);
			Assert::isTrue(($this->id & self::HAVE_PRECISION) > 0);
			
			$this->precision = $precision;
			
			return $this;
		}
		
		public function getScale()
		{
			return $this->scale;
		}
		
		/**
		 * @throws WrongArgumentException
		 * @return DataType
		**/
		public function setScale($scale)
		{
			Assert::isInteger($scale);
			Assert::isTrue(($this->id & self::HAVE_SCALE) > 0);
			
			$this->scale = $scale;
			
			return $this;
		}
		
		/**
		 * @throws WrongArgumentException
		 * @return DataType
		**/
		public function setTimezoned($zoned = false)
		{
			Assert::isTrue(($this->id & self::HAVE_TIMEZONE) > 0);
			
			$this->timezone = true === $zoned;
			
			return $this;
		}
		
		public function isTimezoned()
		{
			return $this->timezone;
		}
		
		/**
		 * @return DataType
		**/
		public function setNull($isNull = false)
		{
			$this->null = $isNull === true;
			
			return $this;
		}
		
		public function isNull()
		{
			return $this->null;
		}
		
		public function typeToString(Dialect $dialect)
		{
			if (
				$this->id == self::BIGINT
				&& $dialect instanceof LiteDialect
			) {
				return $this->names[self::INTEGER];
			} elseif ($this->id == self::FULLTEXT) {
				if ($dialect instanceof PostgresDialect)
					return 'tsvector';
				
				throw new UnimplementedFeatureException();
			}
			
			return $this->name;
		}
		
		public function toDialectString(Dialect $dialect)
		{
			$out = $this->typeToString($dialect);
			
			if ($this->id & self::HAVE_SIZE) {
				
				if (!$this->size)
					throw new WrongStateException(
						"type '{$this->name}' should have size set"
					);
				
				$out .= "({$this->size})";
			}
			
			if ($this->id & self::HAVE_PRECISION) {
				
				if ($this->precision) {
					
					switch ($this->id) {
						
						case self::TIME:
						case self::TIMESTAMP:
							
							$out .= "({$this->precision})";
							break;
						
						case self::NUMERIC:
							
							$out .=
								$this->scale
									? "({$this->precision}, {$this->scale})"
									: "({$this->precision})";
							break;
						
						default:
							
							throw new WrongStateException();
					}
					
				}
			}
			
			if ($this->id & self::HAVE_TIMEZONE)
				$out .= $dialect->timeZone($this->timezone);
			
			$out .=
				$this->null
					? " NULL"
					: " NOT NULL";
			
			return $out;
		}
	}
?>