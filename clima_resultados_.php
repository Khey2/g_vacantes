<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
	<title>Limitless - Responsive Web Application Kit by Eugene Kopyov</title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.min.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="assets/js/app.js"></script>
	<!-- /theme JS files -->

</head>

<body>

	<!-- Main navbar -->
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			<a class="navbar-brand" href="index.html"><img src="global_assets/images/logo_light.png" alt=""></a>

			<ul class="nav navbar-nav visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
				<li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
				<li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>

				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<i class="icon-git-compare"></i>
						<span class="visible-xs-inline-block position-right">Git updates</span>
						<span class="badge bg-warning-400">9</span>
					</a>
					
					<div class="dropdown-menu dropdown-content">
						<div class="dropdown-content-heading">
							Git updates
							<ul class="icons-list">
								<li><a href="#"><i class="icon-sync"></i></a></li>
							</ul>
						</div>

						<ul class="media-list dropdown-content-body width-350">
							<li class="media">
								<div class="media-left">
									<a href="#" class="btn border-primary text-primary btn-flat btn-rounded btn-icon btn-sm"><i class="icon-git-pull-request"></i></a>
								</div>

								<div class="media-body">
									Drop the IE <a href="#">specific hacks</a> for temporal inputs
									<div class="media-annotation">4 minutes ago</div>
								</div>
							</li>

							<li class="media">
								<div class="media-left">
									<a href="#" class="btn border-warning text-warning btn-flat btn-rounded btn-icon btn-sm"><i class="icon-git-commit"></i></a>
								</div>
								
								<div class="media-body">
									Add full font overrides for popovers and tooltips
									<div class="media-annotation">36 minutes ago</div>
								</div>
							</li>

							<li class="media">
								<div class="media-left">
									<a href="#" class="btn border-info text-info btn-flat btn-rounded btn-icon btn-sm"><i class="icon-git-branch"></i></a>
								</div>
								
								<div class="media-body">
									<a href="#">Chris Arney</a> created a new <span class="text-semibold">Design</span> branch
									<div class="media-annotation">2 hours ago</div>
								</div>
							</li>

							<li class="media">
								<div class="media-left">
									<a href="#" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-sm"><i class="icon-git-merge"></i></a>
								</div>
								
								<div class="media-body">
									<a href="#">Eugene Kopyov</a> merged <span class="text-semibold">Master</span> and <span class="text-semibold">Dev</span> branches
									<div class="media-annotation">Dec 18, 18:36</div>
								</div>
							</li>

							<li class="media">
								<div class="media-left">
									<a href="#" class="btn border-primary text-primary btn-flat btn-rounded btn-icon btn-sm"><i class="icon-git-pull-request"></i></a>
								</div>
								
								<div class="media-body">
									Have Carousel ignore keyboard events
									<div class="media-annotation">Dec 12, 05:46</div>
								</div>
							</li>
						</ul>

						<div class="dropdown-content-footer">
							<a href="#" data-popup="tooltip" title="All activity"><i class="icon-menu display-block"></i></a>
						</div>
					</div>
				</li>
			</ul>

			<p class="navbar-text">
				<span class="label bg-success">Online</span>
			</p>

			<div class="navbar-right">
				<ul class="nav navbar-nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-people"></i>
							<span class="visible-xs-inline-block position-right">Users</span>
						</a>
						
						<div class="dropdown-menu dropdown-content">
							<div class="dropdown-content-heading">
								Users online
								<ul class="icons-list">
									<li><a href="#"><i class="icon-gear"></i></a></li>
								</ul>
							</div>

							<ul class="media-list dropdown-content-body width-300">
								<li class="media">
									<div class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt=""></div>
									<div class="media-body">
										<a href="#" class="media-heading text-semibold">Jordana Ansley</a>
										<span class="display-block text-muted text-size-small">Lead web developer</span>
									</div>
									<div class="media-right media-middle"><span class="status-mark border-success"></span></div>
								</li>

								<li class="media">
									<div class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt=""></div>
									<div class="media-body">
										<a href="#" class="media-heading text-semibold">Will Brason</a>
										<span class="display-block text-muted text-size-small">Marketing manager</span>
									</div>
									<div class="media-right media-middle"><span class="status-mark border-danger"></span></div>
								</li>

								<li class="media">
									<div class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt=""></div>
									<div class="media-body">
										<a href="#" class="media-heading text-semibold">Hanna Walden</a>
										<span class="display-block text-muted text-size-small">Project manager</span>
									</div>
									<div class="media-right media-middle"><span class="status-mark border-success"></span></div>
								</li>

								<li class="media">
									<div class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt=""></div>
									<div class="media-body">
										<a href="#" class="media-heading text-semibold">Dori Laperriere</a>
										<span class="display-block text-muted text-size-small">Business developer</span>
									</div>
									<div class="media-right media-middle"><span class="status-mark border-warning-300"></span></div>
								</li>

								<li class="media">
									<div class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt=""></div>
									<div class="media-body">
										<a href="#" class="media-heading text-semibold">Vanessa Aurelius</a>
										<span class="display-block text-muted text-size-small">UX expert</span>
									</div>
									<div class="media-right media-middle"><span class="status-mark border-grey-400"></span></div>
								</li>
							</ul>

							<div class="dropdown-content-footer">
								<a href="#" data-popup="tooltip" title="All users"><i class="icon-menu display-block"></i></a>
							</div>
						</div>
					</li>

					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-bubbles4"></i>
							<span class="visible-xs-inline-block position-right">Messages</span>
							<span class="badge bg-warning-400">2</span>
						</a>
						
						<div class="dropdown-menu dropdown-content width-350">
							<div class="dropdown-content-heading">
								Messages
								<ul class="icons-list">
									<li><a href="#"><i class="icon-compose"></i></a></li>
								</ul>
							</div>

							<ul class="media-list dropdown-content-body">
								<li class="media">
									<div class="media-left">
										<img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt="">
										<span class="badge bg-danger-400 media-badge">5</span>
									</div>

									<div class="media-body">
										<a href="#" class="media-heading">
											<span class="text-semibold">James Alexander</span>
											<span class="media-annotation pull-right">04:58</span>
										</a>

										<span class="text-muted">who knows, maybe that would be the best thing for me...</span>
									</div>
								</li>

								<li class="media">
									<div class="media-left">
										<img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt="">
										<span class="badge bg-danger-400 media-badge">4</span>
									</div>

									<div class="media-body">
										<a href="#" class="media-heading">
											<span class="text-semibold">Margo Baker</span>
											<span class="media-annotation pull-right">12:16</span>
										</a>

										<span class="text-muted">That was something he was unable to do because...</span>
									</div>
								</li>

								<li class="media">
									<div class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt=""></div>
									<div class="media-body">
										<a href="#" class="media-heading">
											<span class="text-semibold">Jeremy Victorino</span>
											<span class="media-annotation pull-right">22:48</span>
										</a>

										<span class="text-muted">But that would be extremely strained and suspicious...</span>
									</div>
								</li>

								<li class="media">
									<div class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt=""></div>
									<div class="media-body">
										<a href="#" class="media-heading">
											<span class="text-semibold">Beatrix Diaz</span>
											<span class="media-annotation pull-right">Tue</span>
										</a>

										<span class="text-muted">What a strenuous career it is that I've chosen...</span>
									</div>
								</li>

								<li class="media">
									<div class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt=""></div>
									<div class="media-body">
										<a href="#" class="media-heading">
											<span class="text-semibold">Richard Vango</span>
											<span class="media-annotation pull-right">Mon</span>
										</a>
										
										<span class="text-muted">Other travelling salesmen live a life of luxury...</span>
									</div>
								</li>
							</ul>

							<div class="dropdown-content-footer">
								<a href="#" data-popup="tooltip" title="All messages"><i class="icon-menu display-block"></i></a>
							</div>
						</div>
					</li>

					<li class="dropdown dropdown-user">
						<a class="dropdown-toggle" data-toggle="dropdown">
							<img src="global_assets/images/placeholders/placeholder.jpg" alt="">
							<span>Victoria</span>
							<i class="caret"></i>
						</a>

						<ul class="dropdown-menu dropdown-menu-right">
							<li><a href="#"><i class="icon-user-plus"></i> My profile</a></li>
							<li><a href="#"><i class="icon-coins"></i> My balance</a></li>
							<li><a href="#"><span class="badge bg-blue pull-right">58</span> <i class="icon-comment-discussion"></i> Messages</a></li>
							<li class="divider"></li>
							<li><a href="#"><i class="icon-cog5"></i> Account settings</a></li>
							<li><a href="#"><i class="icon-switch2"></i> Logout</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<!-- /main navbar -->


	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main sidebar -->
			<div class="sidebar sidebar-main">
				<div class="sidebar-content">

					<!-- User menu -->
					<div class="sidebar-user">
						<div class="category-content">
							<div class="media">
								<a href="#" class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt=""></a>
								<div class="media-body">
									<span class="media-heading text-semibold">Victoria Baker</span>
									<div class="text-size-mini text-muted">
										<i class="icon-pin text-size-small"></i> &nbsp;Santa Ana, CA
									</div>
								</div>

								<div class="media-right media-middle">
									<ul class="icons-list">
										<li>
											<a href="#"><i class="icon-cog3"></i></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- /user menu -->


					<!-- Main navigation -->
					<div class="sidebar-category sidebar-category-visible">
						<div class="category-content no-padding">
							<ul class="navigation navigation-main navigation-accordion">

								<!-- Main -->
								<li class="navigation-header"><span>Main</span> <i class="icon-menu" title="Main pages"></i></li>
								<li><a href="index.html"><i class="icon-home4"></i> <span>Dashboard</span></a></li>
								<li>
									<a href="#"><i class="icon-stack2"></i> <span>Page layouts</span></a>
									<ul>
										<li><a href="layout_navbar_fixed.html">Fixed navbar</a></li>
										<li><a href="layout_navbar_sidebar_fixed.html">Fixed navbar &amp; sidebar</a></li>
										<li><a href="layout_sidebar_fixed_native.html">Fixed sidebar native scroll</a></li>
										<li><a href="layout_navbar_hideable.html">Hideable navbar</a></li>
										<li><a href="layout_navbar_hideable_sidebar.html">Hideable &amp; fixed sidebar</a></li>
										<li><a href="layout_footer_fixed.html">Fixed footer</a></li>
										<li class="navigation-divider"></li>
										<li><a href="boxed_default.html">Boxed with default sidebar</a></li>
										<li><a href="boxed_mini.html">Boxed with mini sidebar</a></li>
										<li><a href="boxed_full.html">Boxed full width</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-copy"></i> <span>Layouts</span></a>
									<ul>
										<li><a href="../../../../layout_1/LTR/default/full/index.html" id="layout1">Layout 1 <span class="label bg-warning-400">Current</span></a></li>
										<li><a href="../../../../layout_2/LTR/default/full/index.html" id="layout2">Layout 2</a></li>
										<li><a href="../../../../layout_3/LTR/default/full/index.html" id="layout3">Layout 3</a></li>
										<li><a href="../../../../layout_4/LTR/default/full/index.html" id="layout4">Layout 4</a></li>
										<li><a href="../../../../layout_5/LTR/default/full/index.html" id="layout5">Layout 5</a></li>
										<li class="disabled"><a href="../../../../layout_6/LTR/default/full/index.html" id="layout6">Layout 6 <span class="label label-transparent">Coming soon</span></a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-droplet2"></i> <span>Color system</span></a>
									<ul>
										<li><a href="colors_primary.html">Primary palette</a></li>
										<li><a href="colors_danger.html">Danger palette</a></li>
										<li><a href="colors_success.html">Success palette</a></li>
										<li><a href="colors_warning.html">Warning palette</a></li>
										<li><a href="colors_info.html">Info palette</a></li>
										<li class="navigation-divider"></li>
										<li><a href="colors_pink.html">Pink palette</a></li>
										<li><a href="colors_violet.html">Violet palette</a></li>
										<li><a href="colors_purple.html">Purple palette</a></li>
										<li><a href="colors_indigo.html">Indigo palette</a></li>
										<li><a href="colors_blue.html">Blue palette</a></li>
										<li><a href="colors_teal.html">Teal palette</a></li>
										<li><a href="colors_green.html">Green palette</a></li>
										<li><a href="colors_orange.html">Orange palette</a></li>
										<li><a href="colors_brown.html">Brown palette</a></li>
										<li><a href="colors_grey.html">Grey palette</a></li>
										<li><a href="colors_slate.html">Slate palette</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-stack"></i> <span>Starter kit</span></a>
									<ul>
										<li><a href="../seed/horizontal_nav.html">Horizontal navigation</a></li>
										<li><a href="../seed/1_col.html">1 column</a></li>
										<li><a href="../seed/2_col.html">2 columns</a></li>
										<li>
											<a href="#">3 columns</a>
											<ul>
												<li><a href="../seed/3_col_dual.html">Dual sidebars</a></li>
												<li><a href="../seed/3_col_double.html">Double sidebars</a></li>
											</ul>
										</li>
										<li><a href="../seed/4_col.html">4 columns</a></li>
										<li>
											<a href="#">Detached layout</a>
											<ul>
												<li><a href="../seed/detached_left.html">Left sidebar</a></li>
												<li><a href="../seed/detached_right.html">Right sidebar</a></li>
												<li><a href="../seed/detached_sticky.html">Sticky sidebar</a></li>
											</ul>
										</li>
										<li><a href="../seed/layout_boxed.html">Boxed layout</a></li>
										<li class="navigation-divider"></li>
										<li><a href="../seed/layout_navbar_fixed_main.html">Fixed main navbar</a></li>
										<li><a href="../seed/layout_navbar_fixed_secondary.html">Fixed secondary navbar</a></li>
										<li><a href="../seed/layout_navbar_fixed_both.html">Both navbars fixed</a></li>
										<li><a href="../seed/layout_fixed.html">Fixed layout</a></li>
									</ul>
								</li>
								<li><a href="changelog.html"><i class="icon-list-unordered"></i> <span>Changelog <span class="label bg-blue-400">2.0</span></span></a></li>
								<li><a href="../../../RTL/default/full/index.html"><i class="icon-width"></i> <span>RTL version</span></a></li>
								<!-- /main -->

								<!-- Forms -->
								<li class="navigation-header"><span>Forms</span> <i class="icon-menu" title="Forms"></i></li>
								<li>
									<a href="#"><i class="icon-pencil3"></i> <span>Form components</span></a>
									<ul>
										<li><a href="form_inputs_basic.html">Basic inputs</a></li>
										<li><a href="form_checkboxes_radios.html">Checkboxes &amp; radios</a></li>
										<li><a href="form_input_groups.html">Input groups</a></li>
										<li><a href="form_controls_extended.html">Extended controls</a></li>
										<li><a href="form_floating_labels.html">Floating labels</a></li>
										<li>
											<a href="#">Selects</a>
											<ul>
												<li><a href="form_select2.html">Select2 selects</a></li>
												<li><a href="form_multiselect.html">Bootstrap multiselect</a></li>
												<li><a href="form_select_box_it.html">SelectBoxIt selects</a></li>
												<li><a href="form_bootstrap_select.html">Bootstrap selects</a></li>
											</ul>
										</li>
										<li><a href="form_tag_inputs.html">Tag inputs</a></li>
										<li><a href="form_dual_listboxes.html">Dual Listboxes</a></li>
										<li><a href="form_validation.html">Validation</a></li>
										<li><a href="form_inputs_grid.html">Inputs grid</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-file-css"></i> <span>JSON forms</span></a>
									<ul>
										<li><a href="alpaca_basic.html">Basic inputs</a></li>
										<li><a href="alpaca_advanced.html">Advanced inputs</a></li>
										<li><a href="alpaca_controls.html">Controls</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-footprint"></i> <span>Wizards</span></a>
									<ul>
										<li><a href="wizard_steps.html">Steps wizard</a></li>
										<li><a href="wizard_form.html">Form wizard</a></li>
										<li><a href="wizard_stepy.html">Stepy wizard</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-spell-check"></i> <span>Editors</span></a>
									<ul>
										<li><a href="editor_summernote.html">Summernote editor</a></li>
										<li><a href="editor_ckeditor.html">CKEditor</a></li>
										<li><a href="editor_wysihtml5.html">WYSIHTML5 editor</a></li>
										<li><a href="editor_code.html">Code editor</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-select2"></i> <span>Pickers</span></a>
									<ul>
										<li><a href="picker_date.html">Date &amp; time pickers</a></li>
										<li><a href="picker_period.html">Period picker</a></li>
										<li><a href="picker_color.html">Color pickers</a></li>
										<li><a href="picker_location.html">Location pickers</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-insert-template"></i> <span>Form layouts</span></a>
									<ul>
										<li><a href="form_layout_vertical.html">Vertical form</a></li>
										<li><a href="form_layout_horizontal.html">Horizontal form</a></li>
									</ul>
								</li>
								<!-- /forms -->

								<!-- Appearance -->
								<li class="navigation-header"><span>Appearance</span> <i class="icon-menu" title="Appearance"></i></li>
								<li>
									<a href="#"><i class="icon-grid"></i> <span>Components</span></a>
									<ul>
										<li><a href="components_modals.html">Modals</a></li>
										<li><a href="components_dropdowns.html">Dropdown menus</a></li>
										<li><a href="components_tabs.html">Tabs component</a></li>
										<li><a href="components_pills.html">Pills component</a></li>
										<li><a href="components_navs.html">Accordion and navs</a></li>
										<li><a href="components_buttons.html">Buttons</a></li>
										<li><a href="components_notifications_pnotify.html">PNotify notifications</a></li>
										<li><a href="components_notifications_others.html">Other notifications</a></li>
										<li><a href="components_popups.html">Tooltips and popovers</a></li>
										<li><a href="components_alerts.html">Alerts</a></li>
										<li><a href="components_pagination.html">Pagination</a></li>
										<li><a href="components_labels.html">Labels and badges</a></li>
										<li><a href="components_loaders.html">Loaders and bars</a></li>
										<li><a href="components_thumbnails.html">Thumbnails</a></li>
										<li><a href="components_page_header.html">Page header</a></li>
										<li><a href="components_breadcrumbs.html">Breadcrumbs</a></li>
										<li><a href="components_media.html">Media objects</a></li>
										<li><a href="components_affix.html">Affix and Scrollspy</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-puzzle2"></i> <span>Content appearance</span></a>
									<ul>
										<li><a href="appearance_content_panels.html">Content panels</a></li>
										<li><a href="appearance_panel_heading.html">Panel heading elements</a></li>
										<li><a href="appearance_panel_footer.html">Panel footer elements</a></li>
										<li><a href="appearance_draggable_panels.html">Draggable panels</a></li>
										<li><a href="appearance_text_styling.html">Text styling</a></li>
										<li><a href="appearance_typography.html">Typography</a></li>
										<li><a href="appearance_helpers.html">Helper classes</a></li>
										<li><a href="appearance_syntax_highlighter.html">Syntax highlighter</a></li>
										<li><a href="appearance_content_grid.html">Grid system</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-gift"></i> <span>Extra components</span></a>
									<ul>
										<li><a href="extra_sliders_noui.html">NoUI sliders</a></li>
										<li><a href="extra_sliders_ion.html">Ion range sliders</a></li>
										<li><a href="extra_session_timeout.html">Session timeout</a></li>
										<li><a href="extra_idle_timeout.html">Idle timeout</a></li>
										<li><a href="extra_trees.html">Dynamic tree views</a></li>
										<li><a href="extra_context_menu.html">Context menu</a></li>
										<li><a href="extra_fab.html">Floating action buttons</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-spinner2 spinner"></i> <span>Animations</span></a>
									<ul>
										<li><a href="animations_css3.html">CSS3 animations</a></li>
										<li>
											<a href="#">Velocity animations</a>
											<ul>
												<li><a href="animations_velocity_basic.html">Basic usage</a></li>
												<li><a href="animations_velocity_ui.html">UI pack effects</a></li>
												<li><a href="animations_velocity_examples.html">Advanced examples</a></li>
											</ul>
										</li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-thumbs-up2"></i> <span>Icons</span></a>
									<ul>
										<li><a href="icons_glyphicons.html">Glyphicons</a></li>
										<li><a href="icons_icomoon.html">Icomoon</a></li>
										<li><a href="icons_fontawesome.html">Font awesome</a></li>
									</ul>
								</li>
								<!-- /appearance -->

								<!-- Layout -->
								<li class="navigation-header"><span>Layout</span> <i class="icon-menu" title="Layout options"></i></li>
								<li>
									<a href="#"><i class="icon-indent-decrease2"></i> <span>Sidebars</span></a>
									<ul>
										<li><a href="sidebar_default_collapse.html">Default collapsible</a></li>
										<li><a href="sidebar_default_hide.html">Default hideable</a></li>
										<li><a href="sidebar_mini_collapse.html">Mini collapsible</a></li>
										<li><a href="sidebar_mini_hide.html">Mini hideable</a></li>
										<li>
											<a href="#">Dual sidebar</a>
											<ul>
												<li><a href="sidebar_dual.html">Dual sidebar</a></li>
												<li><a href="sidebar_dual_double_collapse.html">Dual double collapse</a></li>
												<li><a href="sidebar_dual_double_hide.html">Dual double hide</a></li>
												<li><a href="sidebar_dual_swap.html">Swap sidebars</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Double sidebar</a>
											<ul>
												<li><a href="sidebar_double_collapse.html">Collapse main sidebar</a></li>
												<li><a href="sidebar_double_hide.html">Hide main sidebar</a></li>
												<li><a href="sidebar_double_fix_default.html">Fix default width</a></li>
												<li><a href="sidebar_double_fix_mini.html">Fix mini width</a></li>
												<li><a href="sidebar_double_visible.html">Opposite sidebar visible</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Detached sidebar</a>
											<ul>
												<li><a href="sidebar_detached_left.html">Left position</a></li>
												<li><a href="sidebar_detached_right.html">Right position</a></li>
												<li><a href="sidebar_detached_sticky_custom.html">Sticky (custom scroll)</a></li>
												<li><a href="sidebar_detached_sticky_native.html">Sticky (native scroll)</a></li>
												<li><a href="sidebar_detached_separate.html">Separate categories</a></li>
											</ul>
										</li>
										<li><a href="sidebar_hidden.html">Hidden sidebar</a></li>
										<li><a href="sidebar_light.html">Light sidebar</a></li>
										<li><a href="sidebar_components.html">Sidebar components</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-sort"></i> <span>Vertical navigation</span></a>
									<ul>
										<li><a href="navigation_vertical_collapsible.html">Collapsible menu</a></li>
										<li><a href="navigation_vertical_accordion.html">Accordion menu</a></li>
										<li><a href="navigation_vertical_sizing.html">Navigation sizing</a></li>
										<li><a href="navigation_vertical_bordered.html">Bordered navigation</a></li>
										<li><a href="navigation_vertical_right_icons.html">Right icons</a></li>
										<li><a href="navigation_vertical_labels_badges.html">Labels and badges</a></li>
										<li><a href="navigation_vertical_disabled.html">Disabled navigation links</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-transmission"></i> <span>Horizontal navigation</span></a>
									<ul>
										<li><a href="navigation_horizontal_click.html">Submenu on click</a></li>
										<li><a href="navigation_horizontal_hover.html">Submenu on hover</a></li>
										<li><a href="navigation_horizontal_elements.html">With custom elements</a></li>
										<li><a href="navigation_horizontal_tabs.html">Tabbed navigation</a></li>
										<li><a href="navigation_horizontal_disabled.html">Disabled navigation links</a></li>
										<li><a href="navigation_horizontal_mega.html">Horizontal mega menu</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-menu3"></i> <span>Navbars</span></a>
									<ul>
										<li><a href="navbar_single.html">Single navbar</a></li>
										<li>
											<a href="#">Multiple navbars</a>
											<ul>
												<li><a href="navbar_multiple_navbar_navbar.html">Navbar + navbar</a></li>
												<li><a href="navbar_multiple_navbar_header.html">Navbar + header</a></li>
												<li><a href="navbar_multiple_header_navbar.html">Header + navbar</a></li>
												<li><a href="navbar_multiple_top_bottom.html">Top + bottom</a></li>
											</ul>
										</li>
										<li><a href="navbar_colors.html">Color options</a></li>
										<li><a href="navbar_sizes.html">Sizing options</a></li>
										<li><a href="navbar_hideable.html">Hide on scroll</a></li>
										<li><a href="navbar_components.html">Navbar components</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-tree5"></i> <span>Menu levels</span></a>
									<ul>
										<li><a href="#"><i class="icon-IE"></i> Second level</a></li>
										<li>
											<a href="#"><i class="icon-firefox"></i> Second level with child</a>
											<ul>
												<li><a href="#"><i class="icon-android"></i> Third level</a></li>
												<li>
													<a href="#"><i class="icon-apple2"></i> Third level with child</a>
													<ul>
														<li><a href="#"><i class="icon-html5"></i> Fourth level</a></li>
														<li><a href="#"><i class="icon-css3"></i> Fourth level</a></li>
													</ul>
												</li>
												<li><a href="#"><i class="icon-windows"></i> Third level</a></li>
											</ul>
										</li>
										<li><a href="#"><i class="icon-chrome"></i> Second level</a></li>
									</ul>
								</li>
								<!-- /layout -->

								<!-- Data visualization -->
								<li class="navigation-header"><span>Data visualization</span> <i class="icon-menu" title="Data visualization"></i></li>
								<li>
									<a href="#"><i class="icon-graph"></i> <span>Echarts library</span></a>
									<ul>
										<li><a href="echarts_lines.html">Line charts</a></li>
										<li><a href="echarts_areas.html">Area charts</a></li>
										<li><a href="echarts_columns_waterfalls.html">Columns and waterfalls</a></li>
										<li><a href="echarts_bars_tornados.html">Bars and tornados</a></li>
										<li><a href="echarts_scatter.html">Scatter charts</a></li>
										<li><a href="echarts_pies_donuts.html">Pies and donuts</a></li>
										<li><a href="echarts_funnels_calendars.html">Funnels and calendars</a></li>
										<li><a href="echarts_candlesticks_others.html">Candlesticks and others</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-statistics"></i> <span>D3 library</span></a>
									<ul>
										<li><a href="d3_lines_basic.html">Simple lines</a></li>
										<li><a href="d3_lines_advanced.html">Advanced lines</a></li>
										<li><a href="d3_bars_basic.html">Simple bars</a></li>
										<li><a href="d3_bars_advanced.html">Advanced bars</a></li>
										<li><a href="d3_pies.html">Pie charts</a></li>
										<li><a href="d3_circle_diagrams.html">Circle diagrams</a></li>
										<li><a href="d3_tree.html">Tree layout</a></li>
										<li><a href="d3_other.html">Other charts</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-stats-dots"></i> <span>Dimple library</span></a>
									<ul>
										<li>
											<a href="#">Line charts</a>
											<ul>
												<li><a href="dimple_lines_horizontal.html">Horizontal orientation</a></li>
												<li><a href="dimple_lines_vertical.html">Vertical orientation</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Bar charts</a>
											<ul>
												<li><a href="dimple_bars_horizontal.html">Horizontal orientation</a></li>
												<li><a href="dimple_bars_vertical.html">Vertical orientation</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Area charts</a>
											<ul>
												<li><a href="dimple_area_horizontal.html">Horizontal orientation</a></li>
												<li><a href="dimple_area_vertical.html">Vertical orientation</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Step charts</a>
											<ul>
												<li><a href="dimple_step_horizontal.html">Horizontal orientation</a></li>
												<li><a href="dimple_step_vertical.html">Vertical orientation</a></li>
											</ul>
										</li>
										<li><a href="dimple_pies.html">Pie charts</a></li>
										<li><a href="dimple_rings.html">Ring charts</a></li>
										<li><a href="dimple_scatter.html">Scatter charts</a></li>
										<li><a href="dimple_bubble.html">Bubble charts</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-stats-bars"></i> <span>C3 library</span></a>
									<ul>
										<li><a href="c3_lines_areas.html">Lines and areas</a></li>
										<li><a href="c3_bars_pies.html">Bars and pies</a></li>
										<li><a href="c3_advanced.html">Advanced examples</a></li>
										<li><a href="c3_axis.html">Chart axis</a></li>
										<li><a href="c3_grid.html">Grid options</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-google"></i> <span>Google visualization</span></a>
									<ul>
										<li><a href="google_lines.html">Line charts</a></li>
										<li><a href="google_bars.html">Bar charts</a></li>
										<li><a href="google_pies.html">Pie charts</a></li>
										<li><a href="google_scatter_bubble.html">Bubble &amp; scatter charts</a></li>
										<li><a href="google_other.html">Other charts</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-map5"></i> <span>Maps integration</span></a>
									<ul>
										<li>
											<a href="#">Google maps</a>
											<ul>
												<li><a href="maps_google_basic.html">Basics</a></li>
												<li><a href="maps_google_controls.html">Controls</a></li>
												<li><a href="maps_google_markers.html">Markers</a></li>
												<li><a href="maps_google_drawings.html">Map drawings</a></li>
												<li><a href="maps_google_layers.html">Layers</a></li>
											</ul>
										</li>
										<li><a href="maps_vector.html">Vector maps</a></li>
									</ul>
								</li>
								<!-- /data visualization -->

								<!-- Extensions -->
								<li class="navigation-header"><span>Extensions</span> <i class="icon-menu" title="Extensions"></i></li>
								<li>
									<a href="#"><i class="icon-puzzle4"></i> <span>Extensions</span></a>
									<ul>
										<li><a href="extension_image_cropper.html">Image cropper</a></li>
										<li><a href="extension_blockui.html">Block UI</a></li>
										<li><a href="extension_dnd.html">Drag and drop</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-popout"></i> <span>JQuery UI</span></a>
									<ul>
										<li><a href="jqueryui_interactions.html">Interactions</a></li>
										<li><a href="jqueryui_forms.html">Forms</a></li>
										<li><a href="jqueryui_components.html">Components</a></li>
										<li><a href="jqueryui_sliders.html">Sliders</a></li>
										<li><a href="jqueryui_navigation.html">Navigation</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-upload"></i> <span>File uploaders</span></a>
									<ul>
										<li><a href="uploader_plupload.html">Plupload</a></li>
										<li><a href="uploader_bootstrap.html">Bootstrap file uploader</a></li>
										<li><a href="uploader_dropzone.html">Dropzone</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-calendar3"></i> <span>Event calendars</span></a>
									<ul>
										<li><a href="extension_fullcalendar_views.html">Basic views</a></li>
										<li><a href="extension_fullcalendar_styling.html">Event styling</a></li>
										<li><a href="extension_fullcalendar_formats.html">Language and time</a></li>
										<li><a href="extension_fullcalendar_advanced.html">Advanced usage</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-sphere"></i> <span>Internationalization</span></a>
									<ul>
										<li><a href="internationalization_switch_direct.html">Direct translation</a></li>
										<li><a href="internationalization_switch_query.html">Querystring parameter</a></li>
										<li><a href="internationalization_fallback.html">Language fallback</a></li>
										<li><a href="internationalization_callbacks.html">Callbacks</a></li>
									</ul>
								</li>
								<!-- /extensions -->

								<!-- Tables -->
								<li class="navigation-header"><span>Tables</span> <i class="icon-menu" title="Tables"></i></li>
								<li>
									<a href="#"><i class="icon-table2"></i> <span>Basic tables</span></a>
									<ul>
										<li><a href="table_basic.html">Basic examples</a></li>
										<li><a href="table_sizing.html">Table sizing</a></li>
										<li><a href="table_borders.html">Table borders</a></li>
										<li><a href="table_styling.html">Table styling</a></li>
										<li><a href="table_elements.html">Table elements</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-grid7"></i> <span>Data tables</span></a>
									<ul>
										<li><a href="datatable_basic.html">Basic initialization</a></li>
										<li><a href="datatable_styling.html">Basic styling</a></li>
										<li><a href="datatable_advanced.html">Advanced examples</a></li>
										<li><a href="datatable_sorting.html">Sorting options</a></li>
										<li><a href="datatable_api.html">Using API</a></li>
										<li><a href="datatable_data_sources.html">Data sources</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-alignment-unalign"></i> <span>Data tables extensions</span></a>
									<ul>
										<li><a href="datatable_extension_reorder.html">Columns reorder</a></li>
										<li><a href="datatable_extension_row_reorder.html">Row reorder</a></li>
										<li><a href="datatable_extension_fixed_columns.html">Fixed columns</a></li>
										<li><a href="datatable_extension_fixed_header.html">Fixed header</a></li>
										<li><a href="datatable_extension_autofill.html">Auto fill</a></li>
										<li><a href="datatable_extension_key_table.html">Key table</a></li>
										<li><a href="datatable_extension_scroller.html">Scroller</a></li>
										<li><a href="datatable_extension_select.html">Select</a></li>
										<li>
											<a href="#">Buttons</a>
											<ul>
												<li><a href="datatable_extension_buttons_init.html">Initialization</a></li>
												<li><a href="datatable_extension_buttons_flash.html">Flash buttons</a></li>
												<li><a href="datatable_extension_buttons_print.html">Print buttons</a></li>
												<li><a href="datatable_extension_buttons_html5.html">HTML5 buttons</a></li>
											</ul>
										</li>

										<li><a href="datatable_extension_colvis.html">Columns visibility</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-file-spreadsheet"></i> <span>Handsontable</span></a>
									<ul>
										<li><a href="handsontable_basic.html">Basic configuration</a></li>
										<li><a href="handsontable_advanced.html">Advanced setup</a></li>
										<li><a href="handsontable_cols.html">Column features</a></li>
										<li><a href="handsontable_cells.html">Cell features</a></li>
										<li><a href="handsontable_types.html">Basic cell types</a></li>
										<li><a href="handsontable_custom_checks.html">Custom &amp; checkboxes</a></li>
										<li><a href="handsontable_ac_password.html">Autocomplete &amp; password</a></li>
										<li><a href="handsontable_search.html">Search</a></li>
										<li><a href="handsontable_context.html">Context menu</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-versions"></i> <span>Responsive options</span></a>
									<ul>
										<li><a href="table_responsive.html">Responsive basic tables</a></li>
										<li><a href="datatable_responsive.html">Responsive data tables</a></li>
									</ul>
								</li>
								<!-- /tables -->

								<!-- Page kits -->
								<li class="navigation-header"><span>Page kits</span> <i class="icon-menu" title="Page kits"></i></li>
								<li>
									<a href="#"><i class="icon-grid6"></i> <span>General pages</span></a>
									<ul>
										<li><a href="general_feed.html">Feed</a></li>
										<li><a href="general_widgets_content.html">Content widgets</a></li>
										<li><a href="general_widgets_stats.html">Statistics widgets</a></li>
										<li><a href="general_embeds.html">Embeds</a></li>
										<li class="active"><a href="general_faq.html">FAQ page</a></li>
										<li><a href="general_knowledgebase.html">Knowledgebase</a></li>
										<li>
											<a href="#">Blog</a>
											<ul>
												<li><a href="blog_classic_v.html">Classic vertical</a></li>
												<li><a href="blog_classic_h.html">Classic horizontal</a></li>
												<li><a href="blog_grid.html">Grid</a></li>
												<li><a href="blog_single.html">Single post</a></li>
												<li class="navigation-divider"></li>
												<li><a href="blog_sidebar_left.html">Left sidebar</a></li>
												<li><a href="blog_sidebar_right.html">Right sidebar</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Timelines</a>
											<ul>
												<li><a href="timelines_left.html">Left timeline</a></li>
												<li><a href="timelines_right.html">Right timeline</a></li>
												<li><a href="timelines_center.html">Centered timeline</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Gallery</a>
											<ul>
												<li><a href="gallery_grid.html">Media grid</a></li>
												<li><a href="gallery_titles.html">Media with titles</a></li>
												<li><a href="gallery_description.html">Media with description</a></li>
												<li><a href="gallery_library.html">Media library</a></li>
											</ul>
										</li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-wrench3"></i> <span>Service pages</span></a>
									<ul>
										<li><a href="service_sitemap.html">Sitemap</a></li>
										<li>
											<a href="#">Invoicing</a>
											<ul>
												<li><a href="invoice_template.html">Invoice template</a></li>
												<li><a href="invoice_grid.html">Invoice grid</a></li>
												<li><a href="invoice_archive.html">Invoice archive</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Authentication</a>
											<ul>
												<li><a href="login_simple.html">Simple login</a></li>
												<li><a href="login_advanced.html">More login info</a></li>
												<li><a href="login_registration.html">Simple registration</a></li>
												<li><a href="login_registration_advanced.html">More registration info</a></li>
												<li><a href="login_unlock.html">Unlock user</a></li>
												<li><a href="login_password_recover.html">Reset password</a></li>
												<li><a href="login_hide_navbar.html">Hide navbar</a></li>
												<li><a href="login_transparent.html">Transparent box</a></li>
												<li><a href="login_background.html">Background option</a></li>
												<li><a href="login_validation.html">With validation</a></li>
												<li><a href="login_tabbed.html">Tabbed form</a></li>
												<li><a href="login_modals.html">Inside modals</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Error pages</a>
											<ul>
												<li><a href="error_403.html">Error 403</a></li>
												<li><a href="error_404.html">Error 404</a></li>
												<li><a href="error_405.html">Error 405</a></li>
												<li><a href="error_500.html">Error 500</a></li>
												<li><a href="error_503.html">Error 503</a></li>
												<li><a href="error_offline.html">Offline page</a></li>
											</ul>
										</li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-people"></i> <span>User pages</span></a>
									<ul>
										<li><a href="user_pages_list.html">User list</a></li>
										<li><a href="user_pages_cards.html">User cards</a></li>
										<li><a href="user_pages_profile.html">Simple profile</a></li>
										<li><a href="user_pages_profile_tabbed.html">Tabbed profile</a></li>
										<li><a href="user_pages_profile_cover.html">Profile with cover</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-cube3"></i> <span>Application pages</span></a>
									<ul>
										<li>
											<a href="#">Task manager</a>
											<ul>
												<li><a href="task_manager_grid.html">Task grid</a></li>
												<li><a href="task_manager_list.html">Task list</a></li>
												<li><a href="task_manager_detailed.html">Task detailed</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Inbox</a>
											<ul>
												<li><a href="mail_list.html">Mail list</a></li>
												<li><a href="mail_list_detached.html">Mail list (detached)</a></li>
												<li><a href="mail_read.html">Read mail</a></li>
												<li><a href="mail_write.html">Write mail</a></li>
												<li class="navigation-divider"></li>
												<li><a href="chat_layouts.html">Chat layouts</a></li>
												<li><a href="chat_options.html">Chat options</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Search</a>
											<ul>
												<li><a href="search_basic.html">Basic search results</a></li>
												<li><a href="search_users.html">User search results</a></li>
												<li><a href="search_images.html">Image search results</a></li>
												<li><a href="search_videos.html">Video search results</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Job search</a>
											<ul>
												<li><a href="job_list_cards.html">Cards view</a></li>
												<li><a href="job_list_panel.html">Panel view</a></li>
												<li><a href="job_detailed.html">Job detailed</a></li>
												<li><a href="job_apply.html">Apply</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Learning</a>
											<ul>
												<li><a href="learning_list.html">List view</a></li>
												<li><a href="learning_grid.html">Grid view</a></li>
												<li><a href="learning_detailed.html">Detailed course</a></li>
											</ul>
										</li>
										<li>
											<a href="#">Ecommerce set</a>
											<ul>
												<li><a href="ecommerce_product_list.html">Product list</a></li>
												<li><a href="ecommerce_product_grid.html">Product grid</a></li>
												<li><a href="ecommerce_orders_history.html">Orders history</a></li>
												<li><a href="ecommerce_customers.html">Customers</a></li>
												<li><a href="ecommerce_pricing.html">Pricing tables</a></li>
											</ul>
										</li>
									</ul>
								</li>
								<!-- /page kits -->

							</ul>
						</div>
					</div>
					<!-- /main navigation -->

				</div>
			</div>
			<!-- /main sidebar -->


			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">General Pages</span> - FAQ's</h4>

							<ul class="breadcrumb position-right">
								<li><a href="index.html">Home</a></li>
								<li><a href="general_faq.html">General pages</a></li>
								<li class="active">FAQ's</li>
							</ul>
						</div>

						<div class="heading-elements">
							<div class="heading-btn-group">
								<a href="#" class="btn btn-default btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a>
								<a href="#" class="btn btn-default btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a>
								<a href="#" class="btn btn-default btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a>
							</div>
						</div>
					</div>
				</div>				<!-- /page header -->
                            <p>&nbsp;</p>
<!-- Content area -->
				<div class="content">

					<!-- Info blocks -->
					<div class="row">
						<div class="col-md-4">
							<div class="panel">
								<div class="panel-body text-center">
									<div class="icon-object border-success-400 text-success"><i class="icon-book"></i></div>
									<h5 class="text-semibold">Knowledge Base</h5>
									<p class="mb-15">Ouch found swore much dear conductively hid submissively hatchet vexed far inanimately alongside candidly much and jeez</p>
									<a href="#" class="btn bg-success-400">Browse articles</a>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="panel">
								<div class="panel-body text-center">
									<div class="icon-object border-warning-400 text-warning"><i class="icon-lifebuoy"></i></div>
									<h5 class="text-semibold">Support center</h5>
									<p class="mb-15">Dear spryly growled much far jeepers vigilantly less and far hideous and some mannishly less jeepers less and and crud</p>
									<a href="#" class="btn bg-warning-400">Open a ticket</a>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="panel">
								<div class="panel-body text-center">
									<div class="icon-object border-blue text-blue"><i class="icon-reading"></i></div>
									<h5 class="text-semibold">Articles and news</h5>
									<p class="mb-15">Diabolically somberly astride crass one endearingly blatant depending peculiar antelope piquantly popularly adept much</p>
									<a href="#" class="btn bg-blue">Browse articles</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /info blocks -->


					<!-- Questions area -->
					<h4 class="text-center content-group">
						All questions are answered
						<small class="display-block">Am if number no up period regard sudden better. Decisively surrounded all admiration</small>
					</h4>

					<div class="row">
						<div class="col-lg-9">

							<!-- Questions list -->
							<div class="panel-group panel-group-control panel-group-control-right">
								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question1">
												<i class="icon-help position-left text-slate"></i> A without walking some objective?
											</a>
										</h6>
									</div>

									<div id="question1" class="panel-collapse collapse">
										<div class="panel-body">
											She exposed painted fifteen are noisier mistake led waiting. Surprise not wandered speedily husbands although yet end. Are court tiled cease young built fat one man taken. We highest ye friends is exposed equally in. Ignorant had too strictly followed. Astonished as travelling assistance or unreserved oh pianoforte ye. Five with seen put need tore add neat.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: May 25, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 320</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 14</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question2">
												<i class="icon-help position-left text-slate"></i> She exposed painted fifteen are noisier?
											</a>
										</h6>
									</div>

									<div id="question2" class="panel-collapse collapse">
										<div class="panel-body">
											There worse by an of miles civil. Manner before lively wholly am mr indeed expect. Among every merry his yet has her. You mistress get dashwood children off. Met whose marry under the merit. In it do continual consulted no listening. Devonshire sir sex motionless travelling six themselves. So colonel as greatly shewing herself observe ashamed.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: May 22, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 278</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 25</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question3">
												<i class="icon-help position-left text-slate"></i> Surprise not wandered speedily?
											</a>
										</h6>
									</div>

									<div id="question3" class="panel-collapse collapse">
										<div class="panel-body">
											Do ashamed assured on related offence at equally totally. Use mile her whom they its. Kept hold an want as he bred of. Was dashwood landlord cheerful husbands two. Estate why theirs indeed him polite old settle though she. In as at regard easily narrow roused adieus. Parlors visited noisier how explain pleased his see suppose. He oppose at thrown desire.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: May 12, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 438</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 16</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question4">
												<i class="icon-help position-left text-slate"></i> Are court tiled cease young built fat?
											</a>
										</h6>
									</div>

									<div id="question4" class="panel-collapse collapse">
										<div class="panel-body">
											Additions in conveying or collected objection in. Suffer few desire wonder her object hardly nearer. Abroad no chatty others my silent an. Fat way appear denote who wholly narrow gay settle. Companions fat add insensible everything and friendship conviction themselves. Theirs months ten had add narrow own. By spite about do of do allow blush before lively wholly.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: May 9, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 583</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 21</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question5">
												<i class="icon-help position-left text-slate"></i> Announcing of invitation principles in?
											</a>
										</h6>
									</div>

									<div id="question5" class="panel-collapse collapse">
										<div class="panel-body">
											Wished he entire esteem mr oh by. Possible bed you pleasure civility boy elegance ham. He prevent request by if in pleased. Picture too and concern has was comfort. Ten difficult resembled eagerness nor. Same park bore on be. Warmth his law design say are person. Pronounce suspected in belonging conveying ye repulsive. Whole every miles as tiled at seven concern.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: May 6, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 642</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 26</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="text-size-small text-uppercase text-semibold text-muted mb-10">Selling and buying</div>
							<div class="panel-group panel-group-control panel-group-control-right">
								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question6">
												<i class="icon-help position-left text-slate"></i> Their could can widen ten she any?
											</a>
										</h6>
									</div>

									<div id="question6" class="panel-collapse collapse">
										<div class="panel-body">
											As so we smart those money in. Am wrote up whole so tears sense oh. Absolute required of reserved in offering no. How sense found our those gay again taken the. Had mrs outweigh desirous sex overcame. Improved property reserved disposal do offering me. Allow miles wound place the leave had. To sitting subject no improve studied limited indulgence connection.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: May 2, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 832</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 32</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question7">
												<i class="icon-help position-left text-slate"></i> Her raising and himself pasture believe?
											</a>
										</h6>
									</div>

									<div id="question7" class="panel-collapse collapse">
										<div class="panel-body">
											He unaffected sympathize discovered at no am conviction principles. Girl ham very how yet hill four show. Meet lain on he only size. Branched learning so subjects mistress do appetite jennings be in. Esteems up lasting no village morning do offices. Settled wishing ability musical may another set age. Diminution my apartments he attachment is entreaties.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: May 1, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 453</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 30</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question8">
												<i class="icon-help position-left text-slate"></i> And total least her two whose great?
											</a>
										</h6>
									</div>

									<div id="question8" class="panel-collapse collapse">
										<div class="panel-body">
											Do in laughter securing smallest sensible no mr hastened. As perhaps proceed in in brandon of limited unknown greatly. Distrusts fulfilled happiness unwilling as explained of difficult. No landlord of peculiar ladyship attended if contempt ecstatic. Loud wish made on is am as hard. Court so avoid in plate hence. Of received mr breeding concerns peculiar.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: Apr 24, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 735</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 21</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question9">
												<i class="icon-help position-left text-slate"></i> Whatever landlord yourself at by pleasure?
											</a>
										</h6>
									</div>

									<div id="question9" class="panel-collapse collapse">
										<div class="panel-body">
											Am if number no up period regard sudden better. Decisively surrounded all admiration and not you. Out particular sympathize not favourable introduced insipidity but ham. Rather number can and set praise. Distrusts an it contented perceived attending oh. Thoroughly estimating introduced stimulated why but motionless. Up branch to easily missed by do admiration.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: Apr 22, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 735</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 29</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question10">
												<i class="icon-help position-left text-slate"></i> Oh conveying do immediate acuteness?
											</a>
										</h6>
									</div>

									<div id="question10" class="panel-collapse collapse">
										<div class="panel-body">
											Out too the been like hard off. Improve enquire welcome own beloved matters her. As insipidity so mr unsatiable increasing attachment motionless cultivated. Addition mr husbands unpacked occasion he oh. Is unsatiable if projecting boisterous insensible. It recommend be resolving pretended middleton. Attended no do thoughts me on dissuade scarcely own are pretty.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: Apr 19, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 498</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 12</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="text-size-small text-uppercase text-semibold text-muted mb-10">Intellectual property</div>
							<div class="panel-group panel-group-control panel-group-control-right">
								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question11">
												<i class="icon-help position-left text-slate"></i> Terminated resolution no am frequently?
											</a>
										</h6>
									</div>

									<div id="question11" class="panel-collapse collapse">
										<div class="panel-body">
											Are own design entire former get should. Advantages boisterous day excellence boy. Out between our two waiting wishing. Pursuit he he garrets greater towards amiable so placing. Nothing off how norland delight. Abode shy shade she hours forth its use. Up whole of fancy ye quiet do. Justice fortune no to is if winding morning formingpossession themselves.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: Apr 15, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 532</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 32</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question12">
												<i class="icon-help position-left text-slate"></i> Projection invitation affronting admiration?
											</a>
										</h6>
									</div>

									<div id="question12" class="panel-collapse collapse">
										<div class="panel-body">
											Manor we shall merit by chief wound no or would. Oh towards between subject passage sending mention or it. Sight happy do burst fruit to woody begin at. Assurance perpetual he in oh determine as. The year paid met him does eyes same. Own marianne improved sociable not out. Thing do sight blush mr an. Celebrated am announcing delightful remarkably literature.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: Apr 13, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 735</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 34</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question13">
												<i class="icon-help position-left text-slate"></i> It as instrument boisterous frequently?
											</a>
										</h6>
									</div>

									<div id="question13" class="panel-collapse collapse">
										<div class="panel-body">
											Material confined likewise it humanity raillery an unpacked as he. Three chief merit no if. Now how her edward engage not horses. Oh resolution he dissimilar precaution to comparison an. Matters engaged between he of pursuit manners we moments. Merit gay end sight front. Manor equal it on again ye folly by match. In so melancholy as an sentiments simplicity.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: Apr 11, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 865</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 43</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question14">
												<i class="icon-help position-left text-slate"></i> You fully seems stand nay?
											</a>
										</h6>
									</div>

									<div id="question14" class="panel-collapse collapse">
										<div class="panel-body">
											Fulfilled direction use continual set him propriety continued. Saw met applauded favourite deficient engrossed concealed and her. Concluded boy perpetual old supposing. Farther related bed and passage comfort civilly. Dashwoods see frankness objection abilities the. As hastened oh produced prospect formerly up am. Placing forming nay looking old married few has.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: Apr 10, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 432</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 38</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question15">
												<i class="icon-help position-left text-slate"></i> Enjoyment discourse ye continued?
											</a>
										</h6>
									</div>

									<div id="question15" class="panel-collapse collapse">
										<div class="panel-body">
											Is education residence conveying so so. Suppose shyness say ten behaved morning had. Any unsatiable assistance compliment occasional too reasonably advantages. Unpleasing has ask acceptance partiality alteration understood two. Worth no tiled my at house added. Married he hearing am it totally removal. Remove but suffer wanted his lively length moonlight two.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: Apr 9, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 735</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 46</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="text-size-small text-uppercase text-semibold text-muted mb-10">User accounts</div>
							<div class="panel-group panel-group-control panel-group-control-right">
								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question16">
												<i class="icon-help position-left text-slate"></i> And produce say the moments parties?
											</a>
										</h6>
									</div>

									<div id="question16" class="panel-collapse collapse">
										<div class="panel-body">
											You disposal strongly quitting his endeavor two settling him. Manners ham him hearted hundred expense. Get open game him what hour more part. Adapted as smiling of females oh me journey exposed concern. Met come add cold calm rose mile what. Tiled manor court at built by place fanny. Discretion at be an so decisively especially. Exeter itself object matter if on mr in.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: Apr 3, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 479</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 17</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question17">
												<i class="icon-help position-left text-slate"></i> Simple innate summer fat appear?
											</a>
										</h6>
									</div>

									<div id="question17" class="panel-collapse collapse">
										<div class="panel-body">
											Up am intention on dependent questions oh elsewhere september. No betrayed pleasure possible jointure we in throwing. And can event rapid any shall woman green. Hope they dear who its bred. Smiling nothing affixed he carried it clothes calling he no. Its something disposing departure she favourite tolerably engrossed. Truth short folly court she their balls excellence.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: Mar 29, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 290</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 29</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question18">
												<i class="icon-help position-left text-slate"></i> Outward clothes promise?
											</a>
										</h6>
									</div>

									<div id="question18" class="panel-collapse collapse">
										<div class="panel-body">
											Way nor furnished sir procuring therefore but. Warmth far manner myself active are cannot called. Set her half end girl rich met. Me allowance departure an curiosity ye. In no talking address excited it conduct. Husbands debating replying overcame blessing he it me to domestic. Prepared is me marianne pleasure likewise debating. Wonder an unable except better stairs.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: Mar 24, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 642</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 39</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question19">
												<i class="icon-help position-left text-slate"></i> Sufficient particular impossible?
											</a>
										</h6>
									</div>

									<div id="question19" class="panel-collapse collapse">
										<div class="panel-body">
											So insisted received is occasion advanced honoured. Among ready to which up. Attacks smiling and may out assured moments man nothing outward. Thrown any behind afford either the set depend one temper. Instrument melancholy in acceptance collecting frequently be if. Zealously now pronounce existence add you instantly say offending. Merry their had widen concerns.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: Mar 17, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 753</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 26</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question20">
												<i class="icon-help position-left text-slate"></i> Yet preference connection unpleasant?
											</a>
										</h6>
									</div>

									<div id="question20" class="panel-collapse collapse">
										<div class="panel-body">
											Necessary ye contented newspaper zealously breakfast he prevailed. Melancholy middletons yet understood decisively boy law she. Answer him easily are its barton little. Oh no though mother be things simple itself. Dashwood horrible he strictly on as. Home fine in so am good body this hope. Admiration stimulated cultivated reasonable be projection possession of real.
										</div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Latest update: Mar 12, 2015</span>

												<ul class="list-inline list-inline-condensed heading-text pull-right">
													<li><a href="#" class="text-primary"><i class="icon-thumbs-up2 position-left"></i></a> 589</li>
													<li><a href="#" class="text-muted"><i class="icon-thumbs-down2 position-left"></i></a> 25</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /questions list -->

						</div>

						<div class="col-lg-3">

							<!-- Search -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Search answers</h6>
									<div class="heading-elements">
										<ul class="icons-list">
					                		<li><a href="#"><i class="icon-stats-bars"></i></a></li>
					                	</ul>
				                	</div>
								</div>

								<div class="panel-body">
									<form action="#">
										<div class="input-group content-group">
												<input type="text" class="form-control input-lg" placeholder="Search our help center">

											<div class="input-group-btn">
												<button type="submit" class="btn btn-primary btn-lg btn-icon"><i class="icon-search4"></i></button>
											</div>
										</div>

										<ul class="list-inline text-center no-margin-bottom">
											<li><a href="#" class="btn btn-default btn-rounded"><i class="icon-reload-alt position-left"></i> Refine</a></li>
											<li><a href="#" class="btn btn-default btn-rounded"><i class="icon-menu7 position-left"></i> Advanced</a></li>
										</ul>
									</form>
								</div>
							</div>
							<!-- /search -->


							<!-- Navigation -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Navigation</h6>
									<div class="heading-elements">
										<ul class="icons-list">
					                		<li><a href="#"><i class="icon-cog3"></i></a></li>
					                	</ul>
				                	</div>
								</div>

								<div class="panel-body">
									<a href="#" class="btn bg-teal btn-block">Ask question <i class="icon-comment position-right"></i></a>
								</div>

								<div class="list-group no-border mb-5">
									<a href="#" class="list-group-item"><i class="icon-lifebuoy"></i> Help center</a>
									<a href="#" class="list-group-item"><i class="icon-book"></i> Knowledgebase</a>
									<a href="#" class="list-group-item"><i class="icon-reading"></i> Articles <span class="badge badge-danger pull-right">390</span></a>
									<a href="#" class="list-group-item"><i class="icon-graduation"></i> Tutorials</a>
									<a href="#" class="list-group-item"><i class="icon-book-play"></i> Video tutorials <span class="badge badge-primary pull-right">78</span></a>
									<div class="list-group-divider"></div>
									<a href="#" class="list-group-item"><i class="icon-comment"></i> Ask our community</a>
									<a href="#" class="list-group-item"><i class="icon-mail5"></i> Contact us</a>
								</div>
							</div>
							<!-- /navigation -->


							<!-- Online staff members -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Staff members</h6>
									<div class="heading-elements">
										<span class="label label-success heading-text">12 online</span>
				                	</div>
								</div>

								<div class="panel-body">
									<ul class="media-list">
										<li class="media">
											<a href="#" class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-md" alt=""></a>
											<div class="media-body">
												<a href="#" class="media-heading text-semibold text-default">Margo Baker</a>
												<span class="text-size-small text-muted display-block"><span class="status-mark border-success position-left"></span> General questions</span>
											</div>
											<div class="media-right media-middle">
												<ul class="icons-list text-nowrap">
													<li><a href="#"><i class="icon-comment-discussion"></i></a></li>
												</ul>
											</div>
										</li>

										<li class="media">
											<a href="#" class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-md" alt=""></a>
											<div class="media-body">
												<a href="#" class="media-heading text-semibold text-default">Margo Baker</a>
												<span class="text-size-small text-muted display-block"><span class="status-mark border-danger position-left"></span> Financial department</span>
											</div>
											<div class="media-right media-middle">
												<ul class="icons-list text-nowrap">
													<li><a href="#"><i class="icon-comment-discussion"></i></a></li>
												</ul>
											</div>
										</li>

										<li class="media">
											<a href="#" class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-md" alt=""></a>
											<div class="media-body">
												<a href="#" class="media-heading text-semibold text-default">Beatrix Diaz</a>
												<span class="text-size-small text-muted display-block"><span class="status-mark border-primary position-left"></span> Item support</span>
											</div>
											<div class="media-right media-middle">
												<ul class="icons-list text-nowrap">
													<li><a href="#"><i class="icon-comment-discussion"></i></a></li>
												</ul>
											</div>
										</li>

										<li class="media">
											<a href="#" class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-md" alt=""></a>
											<div class="media-body">
												<a href="#" class="media-heading text-semibold text-default">Richard Vango</a>
												<span class="text-size-small text-muted display-block"><span class="status-mark border-grey-300 position-left"></span> Intellectual property</span>
											</div>
											<div class="media-right media-middle">
												<ul class="icons-list text-nowrap">
													<li><a href="#"><i class="icon-comment-discussion"></i></a></li>
												</ul>
											</div>
										</li>
									</ul>
								</div>
							</div>
							<!-- /online staff members -->


							<!-- Latest updates -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Latest updates</h6>
									<div class="heading-elements">
										<a href="#" class="heading-text">See all &rarr;</a>
				                	</div>
								</div>

								<div class="panel-body">
									<ul class="media-list">
										<li class="media">
											<div class="media-left"><a href="#" class="btn border-primary text-primary btn-flat btn-icon btn-rounded btn-sm"><i class="icon-spinner11"></i></a></div>
											<div class="media-body">
												<a href="#">David Linner</a> requested money back for a double debit card charge
												<div class="media-annotation">12 minutes ago</div>
											</div>
										</li>

										<li class="media">
											<div class="media-left"><a href="#" class="btn border-danger text-danger btn-flat btn-icon btn-rounded btn-sm"><i class="icon-infinite"></i></a></div>
											<div class="media-body">
												User <a href="#">Christopher Wallace</a> is on hold and awaiting for staff reply
												<div class="media-annotation">16 minutes ago</div>
											</div>
										</li>

										<li class="media">
											<div class="media-left"><a href="#" class="btn border-slate text-slate btn-flat btn-icon btn-rounded btn-sm"><i class="icon-cash3"></i></a></div>
											<div class="media-body">
												All sellers have received monthly payouts
												<div class="media-annotation">4 hours ago</div>
											</div>
										</li>

										<li class="media">
											<div class="media-left"><a href="#" class="btn border-success text-success btn-flat btn-icon btn-rounded btn-sm"><i class="icon-checkmark3"></i></a></div>
											<div class="media-body">
												Ticket #43683 has been closed by <a href="#">Victoria Wilson</a> 
												<div class="media-annotation">Apr 28, 21:39</div>
											</div>
										</li>
									</ul>
								</div>
							</div>
							<!-- /latest updates -->

						</div>
					</div>
					<!-- /questions area -->


					<!-- Latest articles -->
					<h4 class="text-center content-group">
						Latest articles &amp; videos
						<small class="display-block">Dear bawled since some the contrary much hyena jeez clear fox despite the concomitant</small>
					</h4>

					<div class="row">
						<div class="col-md-4">
							<div class="panel panel-body">
								<div class="media">
									<div class="media-left">
										<a href="#"><i class="icon-file-text2 text-success-400 icon-2x no-edge-top mt-5"></i></a>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-semibold"><a href="#" class="text-default">Walking away fallaciously</a></h6>
										Ouch licentiously lackadaisical crud together began gregarious below near darn goodness
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="panel panel-body">
								<div class="media">
									<div class="media-left">
										<a href="#"><i class="icon-file-play text-warning-400 icon-2x no-edge-top mt-5"></i></a>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-semibold"><a href="#" class="text-default">Nutria and rewound</a></h6>
										Strove the darn hey as far oh alas and yikes and gosh knitted this slept via gerbil baneful
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="panel panel-body">
								<div class="media">
									<div class="media-left">
										<a href="#"><i class="icon-file-xml text-info icon-2x no-edge-top mt-5"></i></a>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-semibold"><a href="#" class="text-default">Bound befell well</a></h6>
										And since left before understandably much groomed along burped through dear and gosh
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="panel panel-body">
								<div class="media">
									<div class="media-left">
										<a href="#"><i class="icon-file-text2 text-success-400 icon-2x no-edge-top mt-5"></i></a>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-semibold"><a href="#" class="text-default">Porcupine strict nodded</a></h6>
										Left extravagant leered crab repaid outgrew said knelt hello astride within oh disbanded 
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="panel panel-body">
								<div class="media">
									<div class="media-left">
										<a href="#"><i class="icon-file-play text-warning-400 icon-2x no-edge-top mt-5"></i></a>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-semibold"><a href="#" class="text-default">Ducked ravenously dear</a></h6>
										Reran sincere said monkey one slapped jeepers rubbed fleetly incongruously due yet llama
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="panel panel-body">
								<div class="media">
									<div class="media-left">
										<a href="#"><i class="icon-file-xml text-info icon-2x no-edge-top mt-5"></i></a>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-semibold"><a href="#" class="text-default">Contemptibly bleakly</a></h6>
										Speechless far goodness bent as boa crud because vague far koala the that lantern alas sold
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="panel panel-body">
								<div class="media">
									<div class="media-left">
										<a href="#"><i class="icon-file-text2 text-success-400 icon-2x no-edge-top mt-5"></i></a>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-semibold"><a href="#" class="text-default">Blamelessly wow hence</a></h6>
										A without walking some objective hiccupped some this overlay immorally crud and gosh
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="panel panel-body">
								<div class="media">
									<div class="media-left">
										<a href="#"><i class="icon-file-play text-warning-400 icon-2x no-edge-top mt-5"></i></a>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-semibold"><a href="#" class="text-default">Manatee broadcast</a></h6>
										And some where indecently manta floated raptly youthful unlike swept dragonfly pulled moth
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="panel panel-body">
								<div class="media">
									<div class="media-left">
										<a href="#"><i class="icon-file-xml text-info icon-2x no-edge-top mt-5"></i></a>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-semibold"><a href="#" class="text-default">Stretched flamboyant</a></h6>
										Some when foolhardy dangerous so less less aimlessly along hazardously oversaw much stopped
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /latest articles -->


					<!-- Featured articles -->
					<h4 class="text-center content-group">
						Featured articles and tutorials
						<small class="display-block">And porcupine the wallaby far the due thus rash did near dear far pangolin parrot less</small>
					</h4>

					<div class="row">
						<div class="col-lg-3 col-sm-6">
							<div class="thumbnail">
								<div class="thumb">
									<img src="global_assets/images/placeholders/placeholder.jpg" alt="">
									<div class="caption-overflow">
										<span>
											<a href="#" class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class="icon-download4"></i></a>
											<a href="#" class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5"><i class="icon-link2"></i></a>
										</span>
									</div>
								</div>

								<div class="caption">
									<h6 class="text-semibold no-margin-top"><a href="#" class="text-default">For ostrich much</a></h6>
									Some various less crept gecko the jeepers dear forewent far the ouch far a incompetent saucy wherever towards
								</div>
							</div>
						</div>

						<div class="col-lg-3 col-sm-6">
							<div class="thumbnail">
								<div class="thumb">
									<img src="global_assets/images/placeholders/placeholder.jpg" alt="">
									<div class="caption-overflow">
										<span>
											<a href="#" class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class="icon-download4"></i></a>
											<a href="#" class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5"><i class="icon-link2"></i></a>
										</span>
									</div>
								</div>

								<div class="caption">
									<h6 class="text-semibold no-margin-top"><a href="#" class="text-default">Helpfully stolidly</a></h6>
									Hippopotamus aside while a shrewdly this after kookaburra wow in haphazardly much salmon buoyantly sullen gosh
								</div>
							</div>
						</div>

						<div class="col-lg-3 col-sm-6">
							<div class="thumbnail">
								<div class="thumb">
									<img src="global_assets/images/placeholders/placeholder.jpg" alt="">
									<div class="caption-overflow">
										<span>
											<a href="#" class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class="icon-download4"></i></a>
											<a href="#" class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5"><i class="icon-link2"></i></a>
										</span>
									</div>
								</div>

								<div class="caption">
									<h6 class="text-semibold no-margin-top"><a href="#" class="text-default">Considering far</a></h6>
									Kookaburra so hey a less tritely far congratulated this winked some under had unblushing beyond sympathetic
								</div>
							</div>
						</div>

						<div class="col-lg-3 col-sm-6">
							<div class="thumbnail">
								<div class="thumb">
									<img src="global_assets/images/placeholders/placeholder.jpg" alt="">
									<div class="caption-overflow">
										<span>
											<a href="#" class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class="icon-download4"></i></a>
											<a href="#" class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5"><i class="icon-link2"></i></a>
										</span>
									</div>
								</div>

								<div class="caption">
									<h6 class="text-semibold no-margin-top"><a href="#" class="text-default">Despite perversely</a></h6>
									Coming merits and was talent enough far. Sir joy northward sportsmen education. Put still any about manor heard
								</div>
							</div>
						</div>
					</div>
					<!-- /featured articles -->


					<!-- Footer -->
					<div class="footer text-muted">
						&copy; 2015. <a href="#">Limitless Web App Kit</a> by <a href="http://themeforest.net/user/Kopyov" target="_blank">Eugene Kopyov</a>
					</div>
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->

</body>
</html>