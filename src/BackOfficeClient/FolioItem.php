<?php

namespace FaimMedia\BackOfficeClient;

use FaimMedia\BackOfficeClient;

use UnexpectedValueException;

class FolioItem extends AbstractItem {

	public function delete() {
		$this->request->request('folio/'.$this->getId(), 'DELETE');

		$this->data = null;
	}

	public function save() {
		$data = $this->data;

	// validate data
		$this->validate($data);

		if($this->getId()) {
			$response = $this->request->request('folio/'.$this->getId(), 'PATCH', $data);
		} else {
			$response = $this->request->request('folio', 'POST', $data);
		}

		if($response) {
			$this->isSaved = true;

			return $this->set($response, null, false);
		}

		return false;
	}

	private function validate($data) {

		/*foreach(['name', 'permission_reminder'] as $field) {
			if(empty($data[$field])) {
				throw new UnexpectedValueException('Field `'.$field.'` is required');
			}
		}

		foreach(['campaign_defaults', 'contact'] as $field) {
			if(empty($data[$field]) || !is_array($data[$field])) {
				throw new UnexpectedValueException('Field `'.$field.'` must be a valid array');
			}
		}

		foreach(['company', 'address1', 'city', 'state', 'zip', 'country'] as $field) {
			if(empty($data['contact'][$field])) {
				throw new UnexpectedValueException('Field `contact`.`'.$field.'` has an invalid value');
			}
		}

		foreach(['from_name', 'from_email', 'subject', 'language'] as $field) {
			if(empty($data['campaign_defaults'][$field])) {
				throw new UnexpectedValueException('Field `campaign_defaults`.`'.$field.'` has an invalid value');
			}
		}

		if(!filter_var($data['campaign_defaults']['from_email'], FILTER_VALIDATE_EMAIL)) {
			throw new UnexpectedValueException('Field `campaign_defaults`.`from_email` is not a valid email address');
		}

		if(!is_bool($data['email_type_option'])) {
			throw new UnexpectedValueException('Field `email_type_option` must be a boolean');
		}*/
	}
}