<?php
/*
	Question2Answer Tag Synonyms plugin, v1.2
	License: http://www.gnu.org/licenses/gpl.html
*/

class qa_html_theme_layer extends qa_html_theme_base
{
	function option_default($option)
	{
		switch($option)
		{
			default:
				return false;
		}

	}

	// theme replacement functions

	function head_script()
	{
		qa_html_theme_base::head_script();

		if ( $this->forbid_new_tag() )
		{
			$this->output_raw(
				"<script>\n" .
				"function qa_tag_verify()\n" .
				"{\n" .
				"	var tags = jQuery('#tags').val().split(' ');\n" .
				"	var alltags = ','+qa_tags_complete+',';\n" .
				"	if ( jQuery('#tags').siblings('.qa-tag-synonyms-error').length > 0 )\n" .
				"		return false;\n\n" .

				"	for ( var i in tags )\n" .
				"	{\n" .
				"		if ( tags[i].length > 0 && alltags.indexOf(','+tags[i]+',') == -1 )\n" .
				"		{\n" .
				"			var error = '<div style=\"display:none\" class=\"qa-form-tall-error qa-tag-synonyms-error\">The tag \"'+tags[i]+'\" does not exist; you need " . number_format( qa_opt('tag_synonyms_rep') ) . " points to create new tags.</div>';\n" .
				"			jQuery(error).insertAfter('#tags').slideDown('fast').delay(5000).slideUp('fast', function() { jQuery(this).detach() } );\n" .
				"			return false;\n" .
				"		}\n" .
				"	}\n\n" .

				"	document.ask.submit();\n" .
				"}\n" .
				"</script>"
			);
		}
	}

	function form_button_data($button, $key, $style)
	{
		if ( $this->forbid_new_tag() && $key === 'ask' )
		{
			$baseclass='qa-form-'.$style.'-button qa-form-'.$style.'-button-'.$key;
			$hoverclass='qa-form-'.$style.'-hover qa-form-'.$style.'-hover-'.$key;

			$this->output('<INPUT'.rtrim(' '.@$button['tags']).' onclick="qa_tag_verify();" VALUE="'.@$button['label'].'" TITLE="'.@$button['popup'].'" TYPE="button" CLASS="'.$baseclass.'" onmouseover="this.className=\''.$hoverclass.'\';" onmouseout="this.className=\''.$baseclass.'\';"/>');
		}
		else
			qa_html_theme_base::form_button_data($button, $key, $style);
	}

	// worker functions

	function forbid_new_tag()
	{
		$q_edit = $this->template == 'ask' || isset( $this->content['form_q_edit'] );
		$tag_prevent = qa_opt('tag_synonyms_prevent');

		if ( $q_edit && $tag_prevent )
		{
			return
				qa_get_logged_in_points() < (int) qa_opt('tag_synonyms_rep') &&
				qa_get_logged_in_level() < QA_USER_LEVEL_EXPERT;
		}

		return false;
	}

}
