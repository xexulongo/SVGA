<?php

class UsuarisvgaController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('view', 'create', 'login','activate'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('update', 'logout'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'roles'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$this->layout='//layouts/main';
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if (!Yii::app() -> user -> isGuest) {
			Yii::app() -> user -> setFlash('success', Yii::t('hst2', "Ya tienes la sesión iniciada!"));
			$this -> redirect($this -> createUrl(Yii::app()->request->urlReferrer));
		} else{
			$user=new Usuarisvga('register');
			if(isset($_POST['Usuarisvga']))
			{
				$user->attributes=$_POST['Usuarisvga'];
				$user->activated = 1;
				if($user->validate()){
					$user -> email_token = Yii::app() -> token -> createUnique(40, Usuarisvga::model(), 'email_token');
						$user -> email_activated = 0;
						$user -> save(false);

						//enviem el correu amb la direcció per activar el compte
						$message = new YiiMailMessage();
						$message -> setFrom(array('jlexposito7@gmail.com' => 'José Luis Expósito Robles'));
						$message -> setTo(array($user -> email => $user -> username));
						$message -> subject = Yii::t('hst2', 'Activeu el vostre compte');
						$message -> view = 'email_activate';
						$message -> setBody(array('username' => $user -> username, 'token' => $user -> email_token), 'text/html');
						Yii::app() -> mail -> send($message);

						Yii::app() -> user -> setFlash('success', Yii::t('hst2', "S'ha completat correctament el registre. T'hem enviat un correu electrònic amb instruccions sobre com activar el teu compte."));

						if($user->save(false))
							$this->redirect(array('view','id'=>$user->id));
				}
				else {	
					$this->render('create',array(
						'model'=>$user,
					));
				}
			} 
			else {
				$this->render('create',array(
					'model'=>$user,
				));
			}
		}
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$this->layout='//layouts/main';
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Usuarisvga']))
		{
			$model->attributes=$_POST['Usuarisvga'];
			$model->password = md5($model->password);
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		if(Yii::app()->user->checkAccess('admin', array(Yii::app()->user->id)) and $id != Yii::app()->user->id){
			$this->render('_updateadmin',array(
			'model'=>$model,
			));
		}
		else $this->render('update',array(
			'model'=>$model,
			));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Usuarisvga');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Usuarisvga('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Usuarisvga']))
			$model->attributes=$_GET['Usuarisvga'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Usuarisvga the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Usuarisvga::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

/**
	 * Displays the login page
	 */
	public function actionLogin() {
		$this->layout='//layouts/main';
		//només deixem loggejar si no ho està
		if (!Yii::app() -> user -> isGuest) {
			Yii::app() -> user -> setFlash('warning', Yii::t('hst2', 'Ja tens la sessió iniciada!'));
			$this -> redirect(Yii::app()->baseUrl);
		} 
		else {
			$form = new LoginForm();
			if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['Usuarisvga'])) {
					$user = new UserIdentity($_POST['Usuarisvga']['username'], $_POST['Usuarisvga']['password']);
					$result = $user -> authenticate();
					if ($result == PasswordIdentity::CREDENTIALS_ERROR) {
						Yii::app() -> user -> setFlash('error', Yii::t('hst2', 'Usuari i/o contrasenya incorrecte'));
					}
					else if ($result == PasswordIdentity::NOT_ACTIVATED) {
						Yii::app() -> user -> setFlash('warning', Yii::t('hst2', 'El teu usuari encara no estas activat. Contacta amb el administrador per més informació'));
					}
					else if ($result == PasswordIdentity::EMAIL_NOT_ACTIVATED) {
						Yii::app() -> user -> setFlash('errpr', Yii::t('hst2', 'El teu email encara no està activat'));
					}
					else if ($result == PasswordIdentity::OK) {

						Yii::app() -> user -> login($user);
						Yii::app() -> user -> setFlash('success', Yii::t('hst2', 'Sessió iniciada correctament!'));
						$this -> redirect(Yii::app() -> user -> returnUrl);
					} 
					else {
						Yii::app() -> user -> setFlash('error', Yii::t('hst2', 'Error en iniciar la sessió'));
				}
				$form -> password = '';
				$this -> render('login', array('formModel' => $form));
			}
			else {
				//mostrem el formulari
				$this -> render('login', array('formModel' => $form));
			}
		}
	}
	public function actionLogout()
	{
		$user = new UserIdentity(Yii::app()->user->getState('username'), '');
		$user->updatelogin();	//Refresca l'últim login i el camp d'actiu
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionActivate() {

		if (!empty($_GET['token'])) {
			$user = Usuarisvga::model()->find('email_token = :token', array(':token' => $_GET['token']));
			if (empty($user)) {
				Yii::app()->user->setFlash('danger', Yii::t('svga', "No existeix cap usuari amb aquest token"));
				$this->redirect($this -> createUrl(Yii::app() -> homeUrl));
			} else {
				$user->email_activated = 1;
				if ($user->save(false)) {
					Yii::app()->user->setFlash('success', Yii::t('svga', "Hem activat el teu compte correctament. Ja pots iniciar sessió! :)"));
					$this->redirect(Yii::app() -> homeUrl);
				} else {
					Yii::app()->user->setFlash('danger', Yii::t('svga', "Error en activar el teu compte"));
					$this->redirect($this -> createUrl(Yii::app() -> homeUrl));
				}
			}
		} else {
			Yii::app()->user->setFlash('danger', Yii::t('svga', "No ens has passat cap token!"));
			$this -> redirect($this -> createUrl(Yii::app() -> homeUrl));
		}
	}
}
