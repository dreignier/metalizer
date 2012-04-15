<?php
	/**
	 * CacheUtil use a local cache in a member.
	 * Each entity will stay in this cache for the following duration.
	 * Each use of an entity restart the timer.
	 * Specify the duration in millisecond.
	 * @var integer
	 */
	$config['cache.local.life_time'] = 10 * MINUTE * IN_MILLISECONDS;
	
	/**
	 * Cache files are cleared at this interval. In millisecond.
	 * @var integer
	 */
	$config['cache.file.clean_interval'] = DAY * IN_MILLISECONDS;