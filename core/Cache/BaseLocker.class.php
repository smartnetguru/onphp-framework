<?php
/***************************************************************************
 *   Copyright (C) 2005 by Konstantin V. Arkhipov                          *
 *   voxus@onphp.org                                                       *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 ***************************************************************************/
/* $Id$ */

	abstract class BaseLocker extends Singletone
	{
		protected $pool = array();
		
		/// acquire lock
		abstract public function get($key);
		
		/// release lock
		abstract public function free($key);
		
		/// completely remove lock
		abstract public function drop($key);
		
		/// drop all acquired/released locks
		public function clean()
		{
			foreach (array_keys($this->pool) as $key)
				$this->drop($key);
			
			return true;
		}
	}
?>