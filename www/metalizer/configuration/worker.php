<?php
/**
 * Must contains the names of all workers in the application. Each worker must have its own configuration with the syntax $config["worker.workers.$name.value"]
 * @var array[string]
 */
$config['worker.workers'] = array();

/**
 * The size of the worker pool. The worker are executed before the metalizer mecanics.
 * But metalizer execute only the amount of worker specified here on a single request.
 * @var array int
 */
$config['worker.pool_size'] = 1;

/**
 * An example of a worker configuration
 */

/**
 * If false, the worker is never called.
 * @var boolean
 */
$config['worker.workers.example.enabled'] = false;

/**
 * The class of the worker
 * @var string
 */
$config['worker.workers.example.class'] = 'MyWorker';

/**
 * The worker internal, in second.
 * @var in
 */
$config['worker.workers.example.interval'] = 10 * MINUTE;