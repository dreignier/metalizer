<?php

function model($class)
{
	$class_handler_class = $class.'ClassHandler';
	return new $class_handler_class();
}
