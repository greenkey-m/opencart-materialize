<?php
class ControllerCatalogLabel extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/label');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/label');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/label');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/label');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_label->addLabel($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/label', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/label');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/label');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_label->editLabel($this->request->get['label_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/label', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/label');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/label');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $label_id) {
				$this->model_catalog_label->deleteLabel($label_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/label', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'mld.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'	=> $this->language->get('text_home'),
			'href'	=> $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text'	=> $this->language->get('heading_title'),
			'href'	=> $this->url->link('catalog/label', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/label/add', 'token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/label/delete', 'token=' . $this->session->data['token'] . $url, true);

		$data['labels'] = array();

		$filter_data = array(
			'sort'	=> $sort,
			'order'	=> $order,
			'start'	=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'	=> $this->config->get('config_limit_admin')
		);

		$label_total = $this->model_catalog_label->getTotalLabels();

		$results = $this->model_catalog_label->getLabels($filter_data);

		foreach ($results as $result) {
			$data['labels'][] = array(
				'label_id'		=> $result['label_id'],
				'name'			=> $result['name'],
				'sort_order'	=> $result['sort_order'],
				'edit'			=> $this->url->link('catalog/label/edit', 'token=' . $this->session->data['token'] . '&label_id=' . $result['label_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_materialize'] = $this->language->get('text_materialize');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/label', 'token=' . $this->session->data['token'] . '&sort=mld.name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('catalog/label', 'token=' . $this->session->data['token'] . '&sort=ml.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $label_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/label', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($label_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($label_total - $this->config->get('config_limit_admin'))) ? $label_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $label_total, ceil($label_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/label_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['label_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_materialize'] = $this->language->get('text_materialize');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_color'] = $this->language->get('entry_color');
		$data['entry_color_text'] = $this->language->get('entry_color_text');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'	=> $this->language->get('text_home'),
			'href'	=> $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text'	=> $this->language->get('heading_title'),
			'href'	=> $this->url->link('catalog/label', 'token=' . $this->session->data['token'] . $url, true)
		);

		if (!isset($this->request->get['label_id'])) {
			$data['action'] = $this->url->link('catalog/label/add', 'token=' . $this->session->data['token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/label/edit', 'token=' . $this->session->data['token'] . '&label_id=' . $this->request->get['label_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/label', 'token=' . $this->session->data['token'] . $url, true);

		$this->load->model('extension/module/materialize');

		$data['label_description_colors'] = $this->model_extension_module_materialize->getMaterializeColors();

		$data['label_description_colors_text'] = $this->model_extension_module_materialize->getMaterializeColorsText();

		if (isset($this->request->get['label_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$label_info = $this->model_catalog_label->getLabel($this->request->get['label_id']);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['label_description'])) {
			$data['label_description'] = $this->request->post['label_description'];
		} elseif (isset($this->request->get['label_id'])) {
			$data['label_description'] = $this->model_catalog_label->getLabelDescriptions($this->request->get['label_id']);
		} else {
			$data['label_description'] = array();
		}

		if (isset($this->request->post['label_color'])) {
			$data['label_color'] = $this->request->post['label_color'];
		} elseif (!empty($label_info)) {
			$data['label_color'] = $label_info['label_color'];
		} else {
			$data['label_color'] = 'green';
		}

		if (isset($this->request->post['label_color_text'])) {
			$data['label_color_text'] = $this->request->post['label_color_text'];
		} elseif (!empty($label_info)) {
			$data['label_color_text'] = $label_info['label_color_text'];
		} else {
			$data['label_color_text'] = 'white-text';
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($label_info)) {
			$data['sort_order'] = $label_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/label_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/label')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['label_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 20)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/label')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('catalog/product');

		foreach ($this->request->post['selected'] as $label_id) {
			$product_total = $this->model_catalog_product->getTotalProductsByLabelId($label_id);

			if ($product_total) {
				$this->error['warning'] = sprintf($this->language->get('error_product'), $product_total);
			}
		}

		return !$this->error;
	}
}