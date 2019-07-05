<?php

namespace WpCore\Commands;

use \Cron\Cron;
use \Cron\Executor\Executor;
use \Cron\Job\ShellJob;
use \Cron\Resolver\ArrayResolver;
use \Cron\Schedule\CrontabSchedule;

class Kernel
{
	public $cron;

	public $jobs;

	public function __construct()
	{
		$this->cron = new Cron();
		$this->cron->setExecutor(new Executor());
	}

	public function add($command, $at)
	{
		$job = new ShellJob();
		$job->setCommand($command);
		$job->setSchedule(new CrontabSchedule($at));

		$this->jobs[] = $job;

		return $this;
	}

	public function run()
	{
		$resolver = new ArrayResolver($this->jobs);
		$this->cron->setResolver($resolver);
		$this->cron->run();

		return $this;
	}
}
