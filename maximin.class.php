<?php
/**********************************************************************************
* maximin.class.php                                                               *
***********************************************************************************
* Author: Barzokas Vassilios                                                   	  *
* Site: www.vbarzokas.com 														  *
* Email: contact@vbarzokas.com						                  			  *
* =============================================================================== *
* This program is free software; you may redistribute it and/or modify it under   *
* the terms of the GNU/GPL V3 License.          								  *
*                                                                                 *
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
*                                                                                 *
**********************************************************************************/





/*
 * Pattern recognition algorithm MaxiMin
 *
 * A pattern training method without supervisor based on the use of distances between patterns
 * We consider as P the patterns and as K the amount of patterns P[k], with k=1..K, of the training set S
 * and x[k] the array of values of the pattern P[k]
 * We consider the classes counter t [with t belonging to set of Naturals] with initial value t=1
 *
 * For a working example check at the end of this file
 */

 class Maximin
 {

     function __construct($patterns) 
     {
        $this->patterns = $patterns;
        $this->T = array();
        $this->w = array();
        $this->x = array();
        $this->K = array();
        $this->D = array();
        $this->M = array();
        $this->t = 1;
        
        $this->distances_group = array();
        $this->x_keys = array();
        $this->minimum_between_patterns = array();
        $this->minimum_key_between_patterns = array();
        
        $this->division = null;
     }
     
     /*
      * Take an array of patterns as input and return the array of their distances only
      * @param array $this->patterns
      * @return array $x
      */
     function patternsArray()
     {
        //Number of patterns
        $this->K = sizeof($this->patterns[1]);
        for($k=0;$k<$this->K;$k++)
        {
            //Array x[k] of the pattern P[κ]
            $this->x[$k] = $this->patterns[1][$k];
        }
        
        return $this->x;
     }
     
     /* Step 1: Choose a random pattern P[Tt] = P[T1] (Tt=1,…,Κ) and uppon that we define the first class w[t]=w[1].
      * @param array $this->patterns holds the distances of the patterns
      * @rerturn array $this->w 
      */
     function firstPatternClass()
     { 
        $this->T[$this->t] = 1;
        $this->w[1] = array();
        array_push($this->w[1], $this->patterns[1][$this->T[$this->t]]);
     
        return $this->w;
     }
     
     /*
      * Step 2: Create a set of distances D1 of patterns of S from pattern P[T1]
      * Find the pattern P[T2](T2=1,…,Κ) which has the maximum distance Μ1 from P[T1].
      * @return array $M
      */
     function firstPatternMaxDistance()
     {
        $this->D[1] = array();
        //We create the set of the distances D1 of patterns of S from pattern P[T1]
        for($k=0;$k<$this->K;$k++)
        {
            if($this->t!=$k)
            {
                $d = abs( $this->patterns[1][$this->T[$this->t]] - $this->patterns[1][$k] ); 
                array_push($this->D[1], $d);
            }
        }
        
        //Find the pattern P[T2](τ2=1,…,Κ) which has the maximum distance Μ1 from P[T1]
        $this->T[2] = array_search(max($this->D[1]), $this->D[1]);
        $this->T[2] += 1;
        $this->M[1] = max($this->D[1]);
        
        return $this->M;
     }
     
     /*
      * Step 3:Increase the value of t by one and define the class w[t] with P[Tt] where wt={P[Tt]}
      * @return array $this->w
      */
     function thirdStep()
     {
        $this->t = $this->t+1;
        $this->w[$this->t] = array();
        $exists = false;
        for($i=0;$i<sizeof($this->D[1]);$i++)
        {
            if(isset( $this->w[$i] ))
            {
                if( in_array($this->x[$i], $this->w[$i]) )
                {
                    $exists = true;
                    $tempArray1 = $i;
                    $tempArray2 = array_search($this->x[$i], $this->w[$i]);
                }
            }
        }
        if( $exists == false )
        {
            array_push($this->w[$this->t], $this->patterns[1][$this->T[$this->t]]);
        }
        else if( $exists == true )
        {
            unset($this->w[$tempArray1][$tempArray2]);
        }
        
        return $this->w;
     }
     
     
     /*
      * Step 4: Classify each P[κ] [belonging to S] to classes wi[with i=1,…,t] with the criterion of minimum distance. 
      */
     function fourthStep()
     {
        //Create the set Di of the distances of the patterns of each class wi from the pattern P[Ti] wich defined the class.
        for($i=1;$i<=$this->t;$i++)
        {
            $this->D[$i] = array();
            for($k=0;$k<$this->K;$k++)
            {
                    $d = abs( $this->patterns[1][$this->T[$i]] - $this->patterns[1][$k] ); 
                    array_push($this->D[$i], $d);
            }
        }

        //Group the distances in arrays to find the minimum for each P[]
        //eg all the distances of each P[Tt] from P[0] are on $distances_group[0]
        for($i=0;$i<sizeof($this->D[1]);$i++)
        {
            $this->distances_group[$i] = array();
            for($j=1;$j<=sizeof($this->D);$j++)
            {
                $this->distances_group[$i][$j] = array();
                array_push($this->distances_group[$i][$j],$this->D[$j][$i]);
            }
        }
     }
     
     
     /*
      * Find the minimum of each group
      */
     function findMinimumOfEachGroup()
     {
        for($i=0;$i<sizeof($this->D[1]);$i++)
        {
            $this->minimum_between_patterns[$i] = min($this->distances_group[$i]);
            $this->minimum_key_between_patterns[$i] = array_keys($this->distances_group[$i], min($this->distances_group[$i]));
        }
        
        for($i=1;$i<=$this->t+1;$i++)
        {
            $this->x_keys[$this->minimum_key_between_patterns[$i][0]] = array();
        }
     }
     
     /*
      * Check if each value already exists in $this->w array, if not push it in
      */
     function checkIfAlreadyExists()
     {
        for($i=0;$i<sizeof($this->D[1]);$i++)
        {
            array_push($this->x_keys[$this->minimum_key_between_patterns[$i][0]], array_search($this->x[$i], $this->x));
            if( !in_array($this->x[$i], $this->w[$this->minimum_key_between_patterns[$i][0]]) )
            {
                for($j=0;$j<sizeof($this->D[1]);$j++)
                {
                    for($k=0;$k<sizeof($this->D[1]);$k++)
                    {
                       if(isset($this->w[$this->minimum_key_between_patterns[$j][0]][$k]))
                       {
                           if( in_array($this->x[$i], $this->w[$this->minimum_key_between_patterns[$j][0]]) )
                           {
                               $exists = array_search($this->x[$i], $this->w[$this->minimum_key_between_patterns[$j][0]]);
                               unset($this->w[$this->minimum_key_between_patterns[$j][0]][$k]);
                           }
                       }
                    }
                }
                array_push($this->w[$this->minimum_key_between_patterns[$i][0]], $this->x[$i] );
            }
        }
     }
     
     
     /*
      * Find the max distance between all distances D[i] and the pattern P[k] and divide with previous max value 
      */
     function findMax()
     {
        $max = array();

        $findMaxFrom = array();
        $findKeysFrom = array();

        for($i=1;$i<=$this->t;$i++)
        {
            $findMaxFrom[$i] = array();
            $max_keys[$i] = array();
            $max_key = array();

            for($j=0;$j<sizeof($this->w[$i]);$j++)
            {
                if(isset($this->x_keys[$i][$j]))
                {
                    array_push($findMaxFrom[$i], $this->D[$i][$this->x_keys[$i][$j]]);
                    array_push($findKeysFrom, $this->x_keys[$i][$j]);
                }
            }
            
            if(!empty($findMaxFrom[$i]))
            {
                $max[$i] = max( $findMaxFrom[$i] );
                $max_key[$i] = array_keys($findMaxFrom[$i], max( $findMaxFrom[$i] ) );

                $max_keys[$i] = $this->x_keys[$i][$max_key[$i][0]];
            }
            else
            {
                $max[$i] = null;
                $max_key[$i] = null;

                $max_keys[$i] = null;
            }
        }
        
        $this->M[$this->t] = max($max);
        $max_key = array_keys($max, max($max));
        $max_key = $max_keys[$max_key[0]];

        $next = $this->t+1;
        $current = $this->t;
        $previous = $this->t - 1;

        $this->division = $this->M[$current]/$this->M[$previous];

        $this->T[$next] = $max_key;
     }
     
     /*
     * Map final pattern classes with original pattern names 
      * @return array $this->w_names
     */
    function classesToPatterns()
    {
        for($i=0;$i<=sizeof($this->w);$i++)
        {
            if(isset($this->w[$i]))
            {
                $this->w[$i] = array_values($this->w[$i]);
            }
        }

        $this->w_names = array();

        for($i=0;$i<=sizeof($this->w);$i++)
        {
            if(isset($this->w[$i]))
            {
                $this->w_names[$i] = array();
                $this->w[$i] = array_values($this->w[$i]);
                for($j=0;$j<=sizeof($this->w[$i]);$j++)
                {
                    if(isset($this->w[$i][$j]))
                    {
                        $position = array_search($this->w[$i][$j], $this->x);
                        array_push($this->w_names[$i], $this->patterns[0][$position]);
                    }
                }
            }
        }
        
        return $this->w_names;
    }
    
    
    /*
     * Implement the maximin algorithm calling each step's corresponding function
     */
    function maxiMin()
    {
        $counter = 0;
        $this->patternsArray();
        $this->firstPatternClass();
        $this->firstPatternMaxDistance();
        do
        {
            $this->thirdStep();
            $this->fourthStep();
            $this->findMinimumOfEachGroup();
            $this->checkIfAlreadyExists();
            $this->findMax();
            $this->classesToPatterns();
            $this->division;
            $counter++;
        }
        while($this->division >= 0.3);//edit this number to adjust sensitivity
        //var_dump($this->classesToPatterns());
        return $this->classesToPatterns();
    }
 }
?>


<?php
	/*
	$patterns = array(0 => array("pattern1", "pattern2", "pattern3", "pattern4", "pattern5"), 1=>array(5, 10, 15, 50, 60));

	$maximin = new Maximin($patterns);

	$result = $maximin->maxiMin($patterns);
	
	var_dump($result);
	*/
?>