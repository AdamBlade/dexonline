{extends "layout-admin.tpl"}

{block "title"}Lexeme cu comentarii{/block}

{block "content"}
  <h3>{$lexems|count} lexeme cu comentarii</h3>

  {foreach $lexems as $l}
    {include "bits/lexemLink.tpl" lexem=$l}
    <ul><li>{$l->comment|escape|regex_replace:'/]]\r?\n(?!$)/':']]</li><li>'}</li></ul>
  {/foreach}    
{/block}
