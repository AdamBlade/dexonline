<?php

class NGram extends BaseObject {

  public static $_table = 'NGram';
  public static $NGRAM_SIZE = 3;
  public static $MAX_MOVE = 2; // Maximum distance an n-gram is allowed to migrate
  public static $LENGTH_DIF = 2; // Maximum length difference between the searched word and the suggested one
  public static $MAX_RESULTS = 20; //Maximum number of suggestions

  // Returns an array of n-grams.
  public static function split($s) {
    $s = str_repeat('#', self::$NGRAM_SIZE - 1) . $s . str_repeat('%', self::$NGRAM_SIZE - 1);
    $len = mb_strlen($s);
    $results = array();
    for ($i = 0; $i < $len - self::$NGRAM_SIZE + 1; $i++) {
      $results[] = mb_substr($s, $i, self::$NGRAM_SIZE);
    }
    return $results;
  }

  public static function searchNGram($cuv) {
    $leng = mb_strlen($cuv);
    $ngramList = self::split($cuv);
    $hash = array();
    foreach($ngramList as $i => $ngram) {
      $lexemIdList = db_getArray(sprintf("select lexemId from NGram where ngram = '%s' and pos between %d and %d",
                                         $ngram, $i - self::$MAX_MOVE, $i + self::$MAX_MOVE));
      $lexemIdList = array_unique($lexemIdList);
      foreach($lexemIdList as $lexemId) {
        if (!isset($hash[$lexemId])) {
          $hash[$lexemId] = 1;
        } else {
          $hash[$lexemId]++;
        }
      }
    }
    arsort($hash);
    $max = current($hash);
    $lexIds = array_keys($hash,$max);
    $finalResult = array();
    foreach ($lexIds as $id) {
      $result = Model::factory('Lexem')->where('id', $id)->where_gte('charLength', $leng - self::$LENGTH_DIF)
	->where_lte('charLength', $leng + self::$LENGTH_DIF)->find_one();
      if ($result && count($finalResult) < self::$MAX_RESULTS) {
        array_push($finalResult, $result);
        if (count($finalResult) == 20)
          break;
      }
    }
    return $finalResult;
  }
}

?>