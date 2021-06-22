<?php

namespace Icinga\Module\Treeview\Controllers;

use Icinga\Data\Filter\Filter;
use Icinga\Data\Filter\FilterEqual;
use Icinga\Module\Monitoring\Controller;
use Icinga\Module\Monitoring\Backend\MonitoringBackend;
use Icinga\Web\Url;
use Icinga\Web\Widget\Tabextension\DashboardAction;
use Icinga\Web\Widget\Tabextension\MenuAction;
use Icinga\Web\Widget\Tabextension\OutputFormat;
use Icinga\Web\Widget\Tabs;

class HostsController extends Controller {

	public function indexAction() {

		$this->hostgroup = $this->params->get('hostgroup', 'hosts');
		$this->getTabs()->activate('hosts');

		$this->setAutorefreshInterval(10);

		if (strtolower($this->params->shift('stateType', 'soft')) === 'hard') {
			$stateColumn = 'host_hard_state';
			$stateChangeColumn = 'host_last_hard_state_change';
		} else {
			$stateColumn = 'host_state';
			$stateChangeColumn = 'host_last_state_change';
		}

		$this->backend = MonitoringBackend::instance();

		$hosts = $this->backend->select()->from('hoststatus', array(
			'host_icon_image',
			'host_icon_image_alt',
			'host_name',
			'host_display_name',
			'host_in_downtime',
			'host_is_flapping',
			'host_last_state_change' => $stateChangeColumn,
			'host_name',
			'host_notifications_enabled',
			'host_obsessing',
			'host_passive_checks_enabled',
			'host_problem',
			'host_state' => $stateColumn,
		));
		$this->applyRestriction('monitoring/filter/objects', $hosts);
		$this->setupFilterControl($hosts);

		$this->setupPaginationControl($hosts);
		$this->setupSortControl(array(
			'host_severity'             => $this->translate('Severity'),
			'host_state'                => $this->translate('Current State'),
			'host_display_name'         => $this->translate('Hostname'),
			'host_address'              => $this->translate('Address'),
			'host_last_check'           => $this->translate('Last Check'),
			'host_last_state_change'    => $this->translate('Last State Change')
		), $hosts);
		$this->setupLimitControl();

		// Handle soft and hard states
		if (strtolower($this->params->shift('stateType', 'soft')) === 'hard') {
			$stateColumn = 'service_hard_state';
			$stateChangeColumn = 'service_last_hard_state_change';
		} else {
			$stateColumn = 'service_state';
			$stateChangeColumn = 'service_last_state_change';
		}

		$services = $this->backend->select()->from('servicestatus', array(
			'host_name',
			'host_display_name',
			'host_state',
			'service_description',
			'service_display_name',
			'service_state'  => $stateColumn,
			'service_in_downtime',
			'service_acknowledged',
			'service_handled',
			'service_output',
			'service_attempt',
			'service_last_state_change' => $stateChangeColumn,
			'service_icon_image',
			'service_icon_image_alt',
			'service_is_flapping',
			'service_state_type',
			'service_handled',
			'service_severity',
			'service_next_update'
		));
		$this->applyRestriction('monitoring/filter/objects', $services);

		$this->view->hosts = $hosts;
		$this->view->services = $services;
	}

	public function getTabs() {
		$tabs = parent::getTabs();
		$tabs->add(
			'hosts',
			array(
				'title' => 'Hosts',
				'url'   => 'treeview/hosts'
			)
		);
		return $tabs;
	}

}

?>
