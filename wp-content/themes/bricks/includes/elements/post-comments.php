<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_Post_Comments extends Element {
	public $category = 'single';
	public $name     = 'post-comments';
	public $icon     = 'ti-comments';

	public function get_label() {
		return esc_html__( 'Comments', 'bricks' );
	}

	public function enqueue_scripts() {
		// Comments reply: Move form below comment user wants to reply to
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		add_filter( 'bricks/comments/author_tag', [ $this, 'comment_author_link' ] );
	}

	/**
	 * Author HTML tag
	 *
	 * @since 1.10
	 */
	public function comment_author_link( $comment_author_tag ) {
		if ( ! empty( $this->settings['commentAuthorTag'] ) ) {
			$custom_tag = Helpers::sanitize_html_tag( $this->settings['commentAuthorTag'], '' );
			if ( $custom_tag ) {
				$comment_author_tag = $custom_tag;
			}
		}

		return $comment_author_tag;

	}

	public function set_control_groups() {
		$this->control_groups['title'] = [
			'title'    => esc_html__( 'Title', 'bricks' ),
			'tab'      => 'content',
			'required' => [ 'source', '!=', 'wordpress' ],
		];

		$this->control_groups['avatar'] = [
			'title'    => esc_html__( 'Avatar', 'bricks' ),
			'tab'      => 'content',
			'required' => [ 'source', '!=', 'wordpress' ],
		];

		$this->control_groups['comment'] = [
			'title'    => esc_html__( 'Comment', 'bricks' ),
			'tab'      => 'content',
			'required' => [ 'source', '!=', 'wordpress' ],
		];

		$this->control_groups['form'] = [
			'title'    => esc_html__( 'Form', 'bricks' ),
			'tab'      => 'content',
			'required' => [ 'source', '!=', 'wordpress' ],
		];

		$this->control_groups['submitButton'] = [
			'title'    => esc_html__( 'Submit button', 'bricks' ),
			'tab'      => 'content',
			'required' => [ 'source', '!=', 'wordpress' ],
		];
	}

	public function set_controls() {
		// Choose between Bricks and WordPress comments (@since 1.8)
		$this->controls['source'] = [
			'tab'         => 'content',
			'label'       => esc_html__( 'Source', 'bricks' ),
			'type'        => 'select',
			'inline'      => true,
			'options'     => [
				'bricks'    => 'Bricks',
				'wordpress' => 'WordPress',
			],
			'placeholder' => 'Bricks',
		];

		// Group: title

		$this->controls['title'] = [
			'tab'     => 'content',
			'group'   => 'title',
			'label'   => esc_html__( 'Show title', 'bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['titleTag'] = [
			'tab'         => 'content',
			'group'       => 'title',
			'label'       => esc_html__( 'HTML tag', 'bricks' ),
			'type'        => 'select',
			'options'     => [
				'div' => 'div',
				'h1'  => 'h1',
				'h2'  => 'h2',
				'h3'  => 'h3',
				'h4'  => 'h4',
				'h5'  => 'h5',
				'h6'  => 'h6',
			],
			'inline'      => true,
			'small'       => true,
			'rerender'    => true,
			'placeholder' => 'h3',
			'required'    => [ 'title', '!=', '' ],
		];

		$this->controls['titleTypography'] = [
			'tab'      => 'content',
			'group'    => 'title',
			'label'    => esc_html__( 'Typography', 'bricks' ),
			'type'     => 'typography',
			'css'      => [
				[
					'property' => 'font',
					'selector' => '.comments-title',
				],
			],
			'required' => [ 'title', '!=', '' ],
		];

		// Group: avatar

		$this->controls['avatar'] = [
			'tab'     => 'content',
			'group'   => 'avatar',
			'label'   => esc_html__( 'Show avatar', 'bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['avatarSize'] = [
			'tab'         => 'content',
			'group'       => 'avatar',
			'label'       => esc_html__( 'Size', 'bricks' ),
			'type'        => 'number',
			'units'       => true,
			'placeholder' => 60,
			'css'         => [
				[
					'property' => 'margin-left',
					'selector' => '.depth-2',
				],
				[
					'property' => 'margin-left',
					'selector' => '.depth-3',
				],
			],
			'rerender'    => true,
		];

		$this->controls['avatarBorder'] = [
			'tab'   => 'content',
			'group' => 'avatar',
			'label' => esc_html__( 'Border', 'bricks' ),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => '.avatar',
				],
			],
		];

		$this->controls['avatarBoxShadow'] = [
			'tab'   => 'content',
			'group' => 'avatar',
			'label' => esc_html__( 'Box shadow', 'bricks' ),
			'type'  => 'box-shadow',
			'css'   => [
				[
					'property' => 'box-shadow',
					'selector' => '.avatar',
				],
			],
		];

		// Group: comment

		$this->controls['commentAuthorTag'] = [
			'tab'         => 'content',
			'group'       => 'comment',
			'label'       => esc_html__( 'Author', 'bricks' ) . ': ' . esc_html__( 'HTML tag', 'bricks' ),
			'type'        => 'select',
			'options'     => [
				'div' => 'div',
				'h1'  => 'h1',
				'h2'  => 'h2',
				'h3'  => 'h3',
				'h4'  => 'h4',
				'h5'  => 'h5',
				'h6'  => 'h6',
			],
			'inline'      => true,
			'small'       => true,
			'rerender'    => true,
			'placeholder' => 'h5',
		];

		$this->controls['commentAuthorTypography'] = [
			'tab'   => 'content',
			'group' => 'comment',
			'label' => esc_html__( 'Author', 'bricks' ) . ': ' . esc_html__( 'Typography', 'bricks' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.comment-author .fn',
				],
			],
		];

		$this->controls['commentMetaTypography'] = [
			'tab'   => 'content',
			'group' => 'comment',
			'label' => esc_html__( 'Meta', 'bricks' ) . ': ' . esc_html__( 'Typography', 'bricks' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.comment-meta',
				],
			],
		];

		$this->controls['commentContentTypography'] = [
			'tab'   => 'content',
			'group' => 'comment',
			'label' => esc_html__( 'Content', 'bricks' ) . ': ' . esc_html__( 'Typography', 'bricks' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.comment-content',
				],
			],
		];

		// FORM

		$this->controls['formTitleSep'] = [
			'tab'   => 'content',
			'group' => 'form',
			'label' => esc_html__( 'Form title', 'bricks' ),
			'type'  => 'separator',
		];

		$this->controls['formTitle'] = [
			'tab'     => 'content',
			'group'   => 'form',
			'label'   => esc_html__( 'Show', 'bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['formTitleTag'] = [
			'tab'         => 'content',
			'group'       => 'form',
			'label'       => esc_html__( 'HTML tag', 'bricks' ),
			'type'        => 'select',
			'options'     => [
				'div' => 'div',
				'h1'  => 'h1',
				'h2'  => 'h2',
				'h3'  => 'h3',
				'h4'  => 'h4',
				'h5'  => 'h5',
				'h6'  => 'h6',
			],
			'inline'      => true,
			'small'       => true,
			'rerender'    => true,
			'placeholder' => 'h4',
			'required'    => [ 'formTitle', '=', true ],
		];

		$this->controls['formTitleText'] = [
			'tab'         => 'content',
			'group'       => 'form',
			'label'       => esc_html__( 'Text', 'bricks' ),
			'type'        => 'text',
			'placeholder' => esc_html__( 'Leave your comment', 'bricks' ),
			'required'    => [ 'formTitle', '=', true ],
		];

		$this->controls['labelSep'] = [
			'tab'   => 'content',
			'group' => 'form',
			'label' => esc_html__( 'Label', 'bricks' ),
			'type'  => 'separator',
		];

		$this->controls['label'] = [
			'tab'     => 'content',
			'group'   => 'form',
			'label'   => esc_html__( 'Show', 'bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['labelTypography'] = [
			'tab'      => 'content',
			'group'    => 'form',
			'label'    => esc_html__( 'Typography', 'bricks' ),
			'type'     => 'typography',
			'css'      => [
				[
					'property' => 'font',
					'selector' => 'label',
				],
			],
			'required' => [ 'label', '=', true ],
		];

		$this->controls['placeholderTypography'] = [
			'tab'      => 'content',
			'group'    => 'form',
			'label'    => esc_html__( 'Placeholder typography', 'bricks' ),
			'type'     => 'typography',
			'css'      => [
				[
					'property' => 'font',
					'selector' => '::placeholder',
				],
			],
			'required' => [ 'label', '=', true ],
		];

		// COOKIES

		$this->controls['cookiesSep'] = [
			'tab'   => 'content',
			'group' => 'form',
			'label' => esc_html__( 'Cookie consent', 'bricks' ),
			'type'  => 'separator',
		];

		$this->controls['cookies'] = [
			'tab'   => 'content',
			'group' => 'form',
			'label' => esc_html__( 'Show', 'bricks' ),
			'type'  => 'checkbox',
		];

		$this->controls['cookiesRequired'] = [
			'tab'      => 'content',
			'group'    => 'form',
			'label'    => esc_html__( 'Required', 'bricks' ),
			'type'     => 'checkbox',
			'required' => [ 'cookies', '=', true ],
		];

		$this->controls['cookiesText'] = [
			'tab'      => 'content',
			'group'    => 'form',
			'label'    => esc_html__( 'Text', 'bricks' ),
			'type'     => 'text',
			'required' => [ 'cookies', '=', true ],
		];

		// FIELDS

		$this->controls['fieldsSep'] = [
			'tab'   => 'content',
			'group' => 'form',
			'label' => esc_html__( 'Fields', 'bricks' ),
			'type'  => 'separator',
		];

		$this->controls['fieldKeys'] = [
			'tab'         => 'content',
			'group'       => 'form',
			'type'        => 'select',
			'options'     => [
				'author' => esc_html__( 'Name', 'bricks' ),
				'email'  => esc_html__( 'Email', 'bricks' ),
				'url'    => esc_html__( 'Website', 'bricks' ),
			],
			'multiple'    => true,
			'placeholder' => esc_html__( 'Name', 'bricks' ) . ', ' . esc_html__( 'Email', 'bricks' ) . ', ' . esc_html__( 'Website', 'bricks' ),
		];

		// FIELD

		$this->controls['fieldSep'] = [
			'tab'   => 'content',
			'group' => 'form',
			'label' => esc_html__( 'Field', 'bricks' ),
			'type'  => 'separator',
		];

		$this->controls['fieldBackgroundColor'] = [
			'tab'   => 'content',
			'group' => 'form',
			'label' => esc_html__( 'Background color', 'bricks' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background-color',
					'selector' => '.form-group input',
				],
				[
					'property' => 'background-color',
					'selector' => '.form-group textarea',
				],
			],
		];

		$this->controls['fieldBorder'] = [
			'tab'   => 'content',
			'group' => 'form',
			'label' => esc_html__( 'Border', 'bricks' ),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => '.form-group input',
				],
				[
					'property' => 'border',
					'selector' => '.form-group textarea',
				],
			],
		];

		$this->controls['fieldTypography'] = [
			'tab'   => 'content',
			'group' => 'form',
			'label' => esc_html__( 'Typography', 'bricks' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.form-group input',
				],
				[
					'property' => 'font',
					'selector' => '.form-group textarea',
				],
			],
		];

		// Added 'resize' option to textarea (@since 1.11)
		$this->controls['fieldResize'] = [
			'tab'         => 'content',
			'group'       => 'form',
			'label'       => esc_html__( 'Textarea', 'bricks' ) . ': ' . esc_html__( 'Resize', 'bricks' ),
			'type'        => 'select',
			'options'     => [
				'none'       => esc_html__( 'None', 'bricks' ),
				'vertical'   => esc_html__( 'Vertical', 'bricks' ),
				'horizontal' => esc_html__( 'Horizontal', 'bricks' ),
				'both'       => esc_html__( 'Both', 'bricks' ),
			],
			'css'         => [
				[
					'property' => 'resize',
					'selector' => '.form-group textarea',
				],
			],
			'placeholder' => esc_html__( 'Vertical', 'bricks' ),
		];

		// Group: submitButton

		$this->controls['submitButtonText'] = [
			'tab'            => 'content',
			'group'          => 'submitButton',
			'label'          => esc_html__( 'Text', 'bricks' ),
			'type'           => 'text',
			'inline'         => true,
			'hasDynamicData' => false,
			'placeholder'    => esc_html__( 'Submit Comment', 'bricks' ),
		];

		$this->controls['submitButtonSize'] = [
			'tab'         => 'content',
			'group'       => 'submitButton',
			'label'       => esc_html__( 'Size', 'bricks' ),
			'type'        => 'select',
			'options'     => $this->control_options['buttonSizes'],
			'inline'      => true,
			'placeholder' => esc_html__( 'Default', 'bricks' ),
		];

		$this->controls['submitButtonStyle'] = [
			'tab'         => 'content',
			'group'       => 'submitButton',
			'label'       => esc_html__( 'Style', 'bricks' ),
			'type'        => 'select',
			'options'     => $this->control_options['styles'],
			'inline'      => true,
			'placeholder' => esc_html__( 'None', 'bricks' ),
			'default'     => 'primary',
		];

		$this->controls['submitButtonBackgroundColor'] = [
			'tab'   => 'content',
			'group' => 'submitButton',
			'label' => esc_html__( 'Background', 'bricks' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background-color',
					'selector' => '.bricks-button',
				],
			],
		];

		$this->controls['submitButtonBorder'] = [
			'tab'   => 'content',
			'group' => 'submitButton',
			'label' => esc_html__( 'Border', 'bricks' ),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => '.bricks-button',
				],
			],
		];

		$this->controls['submitButtonTypography'] = [
			'tab'   => 'content',
			'group' => 'submitButton',
			'label' => esc_html__( 'Typography', 'bricks' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.bricks-button',
				],
			],
		];
	}

	public function render() {
		$settings = $this->settings;
		$source   = $settings['source'] ?? 'bricks';

		// STEP: Render WordPress comments (comments_template() handles the rest)
		if ( $source === 'wordpress' ) {
			ob_start();
			comments_template();
			$comments_template = ob_get_clean();

			echo "<div {$this->render_attributes( '_root' )}>" . $comments_template . '</div>';
			return;
		}

		// STEP: Render Bricks comments
		global $post;
		$post = get_post( $this->post_id );

		// Fetch comments for this post
		$comment_args = [
			'post_id' => get_the_ID(),
			'status'  => 'approve',
		];

		/**
		 * Include unapproved comments
		 *
		 * @see comment-template.php (WP core)
		 *
		 * @since 1.10.3
		 */
		if ( is_user_logged_in() ) {
			$comment_args['include_unapproved'] = [ get_current_user_id() ];
		} else {
			$unapproved_email = wp_get_unapproved_comment_author_email();

			if ( $unapproved_email ) {
				$comment_args['include_unapproved'] = [ $unapproved_email ];
			}
		}

		$comments       = get_comments( $comment_args );
		$comments_count = count( $comments );

		if ( post_password_required() ) {
			return $this->render_element_placeholder(
				[
					'icon-class' => 'ti-key',
					'title'      => esc_html__( 'Password required.', 'bricks' ),
				]
			);
		}

		if ( ! comments_open() && $comments_count === 0 ) {
			return $this->render_element_placeholder(
				[
					'icon-class' => 'ti-comments',
					'title'      => esc_html__( 'Comments are disabled.', 'bricks' ),
				]
			);
		}

		$title_tag = 'h3';

		if ( ! empty( $settings['titleTag'] ) ) {
			$title_tag = Helpers::sanitize_html_tag( $settings['titleTag'], 'h3' );
		}

		echo "<div {$this->render_attributes( '_root' )}>";
		?>
		<div id="comments">
			<div class="bricks-comments-inner">
				<?php
				if ( $comments_count ) {
					// Set comment pagination
					$paged = get_query_var( 'cpage' ) ? get_query_var( 'cpage' ) : 1; // cpage = comments pagination query var

					if ( ! get_query_var( 'cpage' ) ) {
						set_query_var( 'cpage', $paged );
					}

					// Get Comments per page
					$comments_per_page    = get_option( 'comments_per_page' );
					$comments_total_pages = get_comment_pages_count( $comments, $comments_per_page );
					?>

					<?php if ( isset( $settings['title'] ) ) { ?>
					<<?php echo $title_tag; ?> class="comments-title">
						<?php
						// translators: %1$s: number of comments
						printf( esc_html( _n( '1 comment', '%1$s comments', $comments_count, 'bricks' ) ), number_format_i18n( $comments_count ) );
						?>
					</<?php echo $title_tag; ?>>
					<?php } ?>

					<?php if ( $comments_total_pages > 1 && get_option( 'page_comments' ) ) { ?>
					<nav class="navigation comment-navigation" id="comment-nav-above" role="navigation">
						<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'bricks' ); ?></h2>
						<div class="nav-links">
							<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'bricks' ) ); ?></div>
							<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'bricks' ), $comments_total_pages ); ?></div>
						</div>
					</nav>
					<?php } ?>

					<?php
					echo '<ul class="comment-list' . ( ! isset( $settings['avatar'] ) ? ' no-avatar' : '' ) . '">';

					wp_list_comments(
						[
							'walker'            => null,
							'max_depth'         => '10',
							'style'             => 'ul',
							'callback'          => 'bricks_list_comments',
							'end-callback'      => null,
							'type'              => 'comment',
							'reply_text'        => esc_html__( 'Reply', 'bricks' ),
							'page'              => $paged,
							'per_page'          => $comments_per_page,
							'avatar_size'       => isset( $settings['avatarSize'] ) ? intval( $settings['avatarSize'] ) : 60,
							// TODO: Fix ordering for main elements
							// NOTE: This will need to be integrated with the WordPress settings
							'reverse_top_level' => '',
							'reverse_children'  => '',
							'format'            => 'html5',
							'short_ping'        => true,
							'echo'              => true,
							// Custom settings
							'bricks_avatar'     => isset( $settings['avatar'] ),
						],
						$comments
					);
					?>
					</ul>

					<?php if ( $comments_total_pages > 1 && get_option( 'page_comments' ) ) { ?>
					<nav class="navigation comment-navigation" id="comment-nav-below" role="navigation">
						<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'bricks' ); ?></h2>

						<div class="nav-links">
							<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'bricks' ) ); ?></div>
							<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'bricks' ), $comments_total_pages ); ?></div>
						</div>
					</nav>
						<?php
					}
				}

				// Comments are closed
				if ( ! comments_open() && $comments_count && post_type_supports( get_post_type(), 'comments' ) ) {
					echo '<p class="no-comments">' . esc_html__( 'Comments are closed.', 'bricks' ) . '</p>';
				}

				$commenter           = wp_get_current_commenter();
				$required_name_email = get_option( 'require_name_email' );
				$required_attribute  = $required_name_email ? ' required' : '';
				$required_star       = $required_name_email ? ' *' : '';

				$fields = [
					'author' => '',
					'email'  => '',
					'url'    => '',
				];

				// Field keys: WP filter
				$fields = apply_filters( 'comment_form_default_fields', $fields );

				// Field keys: Bricks settings
				if ( isset( $settings['fieldKeys'] ) && is_array( $settings['fieldKeys'] ) ) {
					$fields = [];

					foreach ( $settings['fieldKeys'] as $keys ) {
						$fields[ $keys ] = '';
					}
				}

				foreach ( $fields as $key => $field_html ) {
					$field_html = '<div class="form-group">';

					switch ( $key ) {
						case 'author':
							if ( isset( $settings['label'] ) ) {
								$field_html .= '<label for="author">' . esc_html_x( 'Name', 'Author name', 'bricks' ) . $required_star . '</label>';
								$field_html .= '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '"' . $required_attribute . ' />';
							} else {
								$field_html .= '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '"' . $required_attribute . ' placeholder="' . esc_html_x( 'Name', 'Author name', 'bricks' ) . $required_star . '" />';
							}
							break;

						case 'email':
							if ( isset( $settings['label'] ) ) {
								$field_html .= '<label for="email">' . esc_html__( 'Email', 'bricks' ) . $required_star . '</label>';
								$field_html .= '<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '"' . $required_attribute . ' />';
							} else {
								$field_html .= '<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '"' . $required_attribute . ' placeholder="' . esc_html__( 'Email', 'bricks' ) . $required_star . '" />';
							}
							break;

						case 'url':
							if ( isset( $settings['label'] ) ) {
								$field_html .= '<label for="url">' . esc_html__( 'Website', 'bricks' ) . '</label>';
								$field_html .= '<input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" />';
							} else {
								$field_html .= '<input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" placeholder="' . esc_html__( 'Website', 'bricks' ) . '" />';
							}
							break;
					}

					$field_html .= '</div>';

					$fields[ $key ] = $field_html;
				}

				if ( isset( $settings['label'] ) ) {
					$comment_field =
					'<div class="form-group">
						<label for="comment">' . esc_html__( 'Comment', 'bricks' ) . ' *</label>
						<textarea id="comment" name="comment" cols="45" rows="8" required></textarea>
					</div>';
				} else {
					$comment_field =
					'<div class="form-group">
						<textarea id="comment" name="comment" cols="45" rows="8" required placeholder="' . esc_html__( 'Comment', 'bricks' ) . ' *"></textarea>
					</div>';
				}

				$submit_text = isset( $settings['submitButtonText'] ) ? $settings['submitButtonText'] : esc_html__( 'Submit Comment', 'bricks' );

				$submit_button_classes = [ 'bricks-button' ];

				if ( isset( $settings['submitButtonStyle'] ) ) {
					$submit_button_classes[] = 'bricks-background-' . $settings['submitButtonStyle'];
				}

				if ( isset( $settings['submitButtonSize'] ) ) {
					$submit_button_classes[] = $settings['submitButtonSize'];
				}

				// Show cookie consent field to Bricks form (@since 1.8)
				if ( ! empty( $settings['cookies'] ) ) {
					$cookies_text = esc_html__( 'Save my name, email, and website in this browser for the next time I comment.', 'bricks' );

					if ( ! empty( $settings['cookiesText'] ) ) {
						$cookies_text = $settings['cookiesText'];
					}

					$cookies_required = isset( $settings['cookiesRequired'] ) ? ' required' : '';

					$fields['cookies'] =
					'<div class="form-group comment-form-cookies-consent">
						<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $cookies_required . '>
						<label for="wp-comment-cookies-consent">' . $cookies_text . '</label>
					</div>';
				} else {
					$fields['cookies'] = '';
				}

				$form_title_tag = ! empty( $settings['formTitleTag'] ) ? Helpers::sanitize_html_tag( $settings['formTitleTag'], 'h4' ) : 'h4';

				$custom_args = [
					'title_reply_before'   => "<$form_title_tag id=\"reply-title\" class=\"comment-reply-title\">",
					'title_reply_after'    => "</$form_title_tag>",
					'cancel_reply_link'    => esc_html__( '(Cancel Reply)', 'bricks' ),
					'label_submit'         => $submit_text,
					'comment_notes_before' => '',
					'comment_notes_after'  => '',
					'fields'               => $fields,
					'comment_field'        => $comment_field,
					'class_submit'         => implode( ' ', $submit_button_classes ),
				];

				if ( isset( $settings['formTitle'] ) ) {
					if ( ! empty( $settings['formTitleText'] ) ) {
						$custom_args['title_reply'] = $settings['formTitleText'];
					} else {
						$custom_args['title_reply'] = $comments_count ? esc_html__( 'Leave your comment', 'bricks' ) : esc_html__( 'Leave the first comment', 'bricks' );
					}
				} else {
					$custom_args['title_reply'] = '';
				}

				comment_form( $custom_args );
				?>
			</div>
		</div>
			<?php
			echo '</div>';
	}
}
