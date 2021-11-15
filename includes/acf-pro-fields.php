<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephen
 * Date: 2021-10-20
 * Time: 1:56 PM
 */

namespace ucf_people_directory_external_rss_news\acf_pro_fields;

add_action( 'acf/init', __NAMESPACE__ . '\\create_fields' );

const enable_external       = 'include_news_listings_from_an_external_site';
const group_container       = 'external_news_options';
// note, when using get_post_meta with acf options within a group, you must refer to the key as 'group_container' . '_' . 'inner_option'
const group_use_com         = 'pull_news_from_college_of_medicine';
const group_com_tag         = 'news_tag_slug';
const group_external_rss    = 'alternative_rss_feed';
const group_max_articles    = 'max_articles_to_show';

function create_fields() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {

		acf_add_local_field_group(
			array(
				'key'                   => 'group_618176a2eb75d',
				'title'                 => 'Single person external news',
				'fields'                => array(
					array(
						'key'               => 'field_618176b60056c',
						'label'             => 'Include news listings from an external site',
						'name'              => enable_external,
						'type'              => 'true_false',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'message'           => '',
						'default_value'     => 0,
						'ui'                => 1,
						'ui_on_text'        => '',
						'ui_off_text'       => '',
					),
					array(
						'key'               => 'field_6181770e0056d',
						'label'             => 'External news options',
						'name'              => group_container,
						'type'              => 'group',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_618176b60056c',
									'operator' => '==',
									'value'    => '1',
								),
							),
						),
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'layout'            => 'block',
						'sub_fields'        => array(
							array(
								'key'               => 'field_6181777e0056f',
								'label'             => 'Pull news from College of Medicine',
								'name'              => group_use_com,
								'type'              => 'true_false',
								'instructions'      => '',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'message'           => 'Pull articles from College of Medicine news. Deactivate to specify an alternative RSS feed.',
								'default_value'     => 1,
								'ui'                => 1,
								'ui_on_text'        => 'COM News Feed',
								'ui_off_text'       => 'Alternative Feed',
							),
							array(
								'key'               => 'field_618177d000570',
								'label'             => 'Person ID or URL Slug from COM site',
								'name'              => group_com_tag,
								'type'              => 'text',
								'instructions'      => 'Specify the Person ID or the Person Slug from the COM site that matches the person here. For example, the COM Person ID for Abdo Asmar is "375", and the COM URL Slug is "abdo-asmar-m-d"',
								'required'          => 1,
								'conditional_logic' => array(
									array(
										array(
											'field'    => 'field_6181777e0056f',
											'operator' => '==',
											'value'    => '1',
										),
									),
								),
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '375 OR abdo-asmar-m-d',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
							array(
								'key'               => 'field_6181784000571',
								'label'             => 'Alternative RSS Feed',
								'name'              => group_external_rss,
								'type'              => 'url',
								'instructions'      => 'Specify the full url for an RSS feed to pull in articles from an external site.',
								'required'          => 1,
								'conditional_logic' => array(
									array(
										array(
											'field'    => 'field_6181777e0056f',
											'operator' => '!=',
											'value'    => '1',
										),
									),
								),
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => 'https://med.ucf.edu/feed/?post_type=news&post_associated_people=abdo-asmar-m-d',
							),
							array(
								'key'               => 'field_6181788600572',
								'label'             => 'Max Articles to Show',
								'name'              => group_max_articles,
								'type'              => 'range',
								'instructions'      => 'Maximum number of external articles to show on the profile. Choose a number between 1 and 10. The most recent articles will be shown first.',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => 5,
								'min'               => 1,
								'max'               => 10,
								'step'              => '',
								'prepend'           => '',
								'append'            => '',
							),
						),
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'person',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			)
		);

	}
}