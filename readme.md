# SM-2 for PHP
PHP implementation of SuperMemo 2.0 Algorithm which improves the memorization of knowledge
The learning method is based on cyclical repetitions. The algorithm, based on the assessment of the quality of the answers, calculates the distances in time that will allow for optimal consolidation of knowledge.

## Open Source
SuperMemo is described at supermemo.com. You do not need to license that algorithm. It is open to the public. Only requirement for such cases is a prominent credit given to the authors of SuperMemo. You have to include the following copyright note and site reference regarding the Algorithm SM-2:

Algorithm SM-2, (C) Copyright SuperMemo World, 1991.

https://www.supermemo.com
https://www.supermemo.eu


The algorithm was improved in subsequent versions. Even though the algorithm is good enough, and is best liked by developers for its simplicity.

## Basic Usage
### Creating Repetition from first Response quality assesment
```php
$clock = new SystemClock();
$repetition = Repetition::createFromFirstResponse(Quality::PERFECT, $clock);
```

### Check days interval for next repetition
```php
$repetition->interval();
```

### Check date of next repetition
```php
$repetition->date();
```