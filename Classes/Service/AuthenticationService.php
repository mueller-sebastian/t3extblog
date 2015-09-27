<?php

namespace TYPO3\T3extblog\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2015 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AuthenticationService implements AuthenticationServiceInterface {

	/**
	 * Session data
	 *
	 * @var array
	 */
	protected $sessionData = NULL;

	/**
	 * Session Service
	 *
	 * @var \TYPO3\T3extblog\Service\SessionServiceInterface
	 * @inject
	 */
	protected $session;

	/**
	 *
	 * @return boolean
	 */
	public function isValid() {
		if ($this->getEmail()) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 *
	 * @param string $email
	 *
	 * @return boolean
	 */
	public function login($email) {
		$this->session->setData(
			array(
				'email' => $email
			)
		);

		return TRUE;
	}

	/**
	 *
	 * @return void
	 */
	public function logout() {
		$this->session->removeData();
	}

	/**
	 * Returns email of the subscriber object
	 *
	 * @return string
	 */
	public function getEmail() {
		$data = $this->getData();

		if (!(is_array($data) && array_key_exists('email', $data))) {
			return FALSE;
		}

		if (empty($data['email'])) {
			return FALSE;
		}

		return $data['email'];
	}

	/**
	 * @return array
	 */
	protected function getData() {
		if ($this->sessionData === NULL) {
			$this->sessionData = $this->session->getData();
		}

		return $this->sessionData;
	}
}
