<?php
/*
 Metalizer, a MVC php Framework.
 Copyright (C) 2012 David Reignier

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

/**
 * Represent a model object.
 * @author David Reignier
 *
 */
class Model extends MetalizerObject {
	
	protected $model;
	
	public function initialize() {
		
	}
	
	public function getModel() {
		return $this->model;
	}
	
	public function setModel($model) {
		$this->model = $model;
	}
	
	public function getId() {
		return $this->model->id;
	}
	
	public function store() {
		$this->getClassHandler()->store($this);
	}
	
	public function trash() {
		$this->getClassHandler()->trash($this);
	}
	
	public function getClassHandler() {
		return model($this->getClass());
	}

}