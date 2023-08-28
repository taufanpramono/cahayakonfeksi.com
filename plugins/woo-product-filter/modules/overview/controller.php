<?php
class OverviewControllerWpf extends ControllerWpf {
	public function subscribe() {
		$res = new ResponseWpf();
		if ($this->getModel()->subscribe(ReqWpf::get('post'))) {
			$res->addMessage(esc_html__('Done', 'woo-product-filter'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		$res->ajaxExec();
	}
	public function rating() {
		$res = new ResponseWpf();
		if ($this->getModel()->rating(ReqWpf::get('post'))) {
			$res->addMessage(esc_html__('Done', 'woo-product-filter'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		$res->ajaxExec();
	}
	public function dismissNotice() {
		$res = new ResponseWpf();
		$slug = ReqWpf::getVar('slug');
		if (!empty($slug) && !is_null($slug)) {
			FrameWpf::_()->getModule('options')->getModel()->save('dismiss_' . $slug, 1);
		}
		$res->ajaxExec();
	}
}
