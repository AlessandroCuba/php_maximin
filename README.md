Pattern Recognition in PHP – Maximin Clustering Algorithm
===========
 
A pattern training method without supervisor based on the use of distances between patterns.

We consider as P the patterns and as K the amount of patterns P[k], with k=1..K, of the training set S and x[k] the array of values of the pattern P[k]

We also consider the classes counter t [with t belonging to set of Naturals] with initial value t=1


Working example:
--------------
```
  $patterns = array(0 => array("pattern1", "pattern2", "pattern3", "pattern4", "pattern5"), 1=>array(5, 10, 15, 50, 60));

  $maximin = new Maximin($patterns);

  $result = $maximin->maxiMin($patterns);

  var_dump($result);
```
