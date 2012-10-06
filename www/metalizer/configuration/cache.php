<?php
/**
 * CacheUtil use a local cache in a member.
 * Each entity will stay in this cache for the following duration.
 * Each use of an entity restart the timer.
 * Specify the duration in second.
 * @var integer
 */
$config['cache.local.life_time'] = 10 * MINUTE;

/**
 * Cache files are cleared at this interval. In second.
 * @var integer
 */
$config['cache.file.clean_interval'] = 1 * DAY;
