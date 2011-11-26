Cache - cache php output and objects

This class can cache php text output and objects to files.
Constructor for cache blocks is very simple and it requires only one
  "where" loop.
You can also nest multiple blocks, with different expiration times.
Cache class also includes easy to use Smarty plugin.

Basic PHP syntax:
  
  while($cache->save("cache.sample.tmp",60))
  {
    echo("put some code here!");
  }
