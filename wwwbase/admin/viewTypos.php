<?php
require_once("../../phplib/util.php");
util_assertModerator(PRIV_EDIT);
util_assertNotMirror();
RecentLink::createOrUpdate('Greșeli de tipar');

$sourceClause = '';
$sourceId = 0;
$sourceUrlName = util_getRequestParameter('source');
if ($sourceUrlName) {
  $source = $sourceUrlName ? Source::get_by_urlName($sourceUrlName) : null;
  $sourceId = $source ? $source->id : 0;
  $sourceClause = $source ? "sourceId = {$sourceId} and " : '';
  SmartyWrap::assign('src_selected', $sourceId);
}

$defs = Model::factory('Definition')
  ->raw_query("select * from Definition where {$sourceClause} id in (select definitionId from Typo) order by lexicon")->find_many();

SmartyWrap::assign('searchResults', SearchResult::mapDefinitionArray($defs));
SmartyWrap::assign('recentLinks', RecentLink::loadForUser());
SmartyWrap::displayAdminPage('admin/viewTypos.tpl');

?>
