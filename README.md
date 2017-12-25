# LambdaPHP Installer

This is the composer package that can be used in your lambdaphp sites for using DynamoDB as cache.

## Usage

    <?php
    
    use LambdaPhp\Cache;
    
    $cache = new Cache();
    $result = $cache->get('some-key', function() { return 'fresh result'; });
    
