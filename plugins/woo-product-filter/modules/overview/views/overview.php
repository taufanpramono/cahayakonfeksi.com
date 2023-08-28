<?php
class OverviewViewWpf extends ViewWpf {
	public function getOverviewTabContent() {
		FrameWpf::_()->addScript('admin.overview', $this->getModule()->getModPath() . 'js/admin.overview.js');
		
		FrameWpf::_()->getModule('templates')->loadJqueryUi();
		FrameWpf::_()->getModule('templates')->loadBootstrap();
		FrameWpf::_()->addScript('notify-js', WPF_JS_PATH . 'notify.js', array(), false, true);
		FrameWpf::_()->addStyle('admin.overview.css', $this->getModule()->getModPath() . 'css/admin.overview.css');
		
		$this->assign('isWeek', ( time() - $this->getModel()->getFirstOverview() ) > 608800);
		return parent::getContent('overviewTabContent');
	}
	public function showAdminInfo() {
		$dismiss = (int) FrameWpf::_()->getModule('options')->get('dismiss_wpf-ads-reward');
		if ($dismiss) {
			return;	// it was already dismissed by user - no need to show it again
		}
		FrameWpf::_()->getModule('templates')->loadCoreJs();
		FrameWpf::_()->addScript('wpf.admin.notice.dismis', $this->getModule()->getModPath() . 'js/admin.notice.dismis.js');

		$this->assign( 'message',
			'<b>' . esc_html__('New! Reward points and loyalty plugin from WBW', 'woo-product-filter') . '</b><br/>' .
			esc_html__('Set rewards in the form of bonus points for the purchase of good, signup, writing review and more. Create delayed campaigns with automatic reward points accrual based on triggers/conditions.', 'woo-product-filter') .
			' <a href="https://woobewoo.com/plugins/reward-points-for-woocoommerce/" target="_blank">' . esc_html__('More Info', 'woo-product-filter') . '</a>'
		);
		$this->assign('msgSlug', 'wpf-ads-reward');
		HtmlWpf::echoEscapedHtml($this->getContent('showAdminInfo'));
	}
}
