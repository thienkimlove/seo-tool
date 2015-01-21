<?php
/**
 * This is a Engine used to disable caching completly (used for php cli mode to avoid cache issues on deploy)
 */
class BlackHoleEngine extends CacheEngine {

	public function write($key, $value, $duration) {
		return true;
	}

	public function read($key) {
	    return false;
	}

	public function increment($key, $offset = 1) {
		throw new CacheException('Method not supported');
	}

	public function decrement($key, $offset = 1) {
		throw new CacheException('Method not supported');
	}

	public function delete($key) {
	    return true;
	}

	public function clear($check) {
	    return true;
	}

	public function clearGroup($group) {
	    return true;
	}
}
