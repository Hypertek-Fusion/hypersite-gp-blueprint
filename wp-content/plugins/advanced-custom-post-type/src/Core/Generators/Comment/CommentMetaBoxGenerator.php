<?php

namespace ACPT\Core\Generators\Comment;

use ACPT\Core\Generators\AbstractGenerator;
use ACPT\Core\Models\Meta\MetaBoxModel;

class CommentMetaBoxGenerator extends AbstractGenerator
{
	/**
	 * @var MetaBoxModel
	 */
	private MetaBoxModel $boxModel;

	/**
	 * CommentMetaBoxGenerator constructor.
	 *
	 * @param MetaBoxModel $boxModel
	 */
	public function __construct(MetaBoxModel $boxModel)
	{
		$this->boxModel = $boxModel;
	}

	/**
	 * Attach this box to front-end form
	 */
	public function generate()
	{
		add_filter( 'comment_form_defaults', function($default){
			$render = '<div class="acpt-comment-fields-wrapper">';

			foreach (array_reverse($this->boxModel->getFields()) as $metaField){
				$generator = new CommentMetaFieldGenerator($metaField);
				$field = $generator->render();

				if($field !== null){
					$render .= $field;
				}
			}

			$render .= '</div>';

			$default[ 'submit_field' ] = $render . $default[ 'submit_field' ];

			return $default;
		});
	}
}
