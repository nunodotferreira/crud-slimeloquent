<?php 
	session_start();
	require 'vendor/autoload.php';
	
	$app = new \Slim\Slim();
	$app -> config(array(
		'debug' => true,
		'templates.path' => 'views',  
		));

	// instanciando o eloquente
	use Illuminate\Database\Capsule\Manager as Capsule;

	/**
	* database
	*/
	$capsule = new Capsule;
	$capsule->addConnection(array(
		'driver'    => 'mysql',		 
		'host'      => 'localhost',		 
		'database'  => 'pool',		 
		'username'  => 'root',		 
		'password'  => 'root',		 
		'charset'   => 'utf8',		 
		'collation' => 'utf8_general_ci',		 
		'prefix'    => ''
	));
	$capsule->setAsGlobal();
	$capsule->bootEloquent();


	$authentication = function() {
		$app = \Slim\Slim::getInstance();
		$user = $request->getParam('user');
		$pass = $request->getParam('pass');
		// $user = $_GET['user'];
		// $pass = $_GET['pass'];

		// $user = $app->request->headers->get('HTTP_USER');
		// $pass = $app->request->headers->get('HTTP_PASS');
		$pass = sha1($pass);
		// Validando os dados de acesso
		$isvalid = Usuarios::findOne('login', 'user=? AND pass=?', array($user, $pass));
		try {
			if (!$isvalid) {
				throw new Exception("Usuário ou password inválidos", 1);
			} else {
				$app->redirect('all');
			}
		} catch (Exception $e) {
			$app->status(401);
			echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
			$app->stop();
		}
	};


	/**
	* database
	*/
	// $db = new PDO('mysql:host=localhost;dbname=pool;charset=utf8', 'root', 'root');
	
	$app->get('/', function() use($app) {
		$teste = 123456789;
		echo sha1($teste);
		$app->render('home.html');
	})->name('home');
	




	$app->group('/api', function() use($app, $authentication) {
		$app->group('/usuarios', function() use($app, $authentication) {
			$app->response->headers->set('Content=Type','application/json');
			/**
			* Busca de todos os usuarios
			*/
			$app->get('/all', $authentication, function() use($app) {
				//busca de todos os usuários
				$alluser = Usuarios::all();
				$data = json_decode($alluser, true);

				$app->render('usuarios.php', $data);
			})->name('all');

			/**
			* Novo usuário
			*/
			$app->get('/new', $authentication, function () use($app) {
				$app->render('novo.php');
			});
			// insert de usuarios
			$app->post('/new', function() use($app) {
				// obtendo os dados por POST
				try {
					$request = $app->request;
					// pegando os valores do select
					$request = $app->request;
					$nome = $request->post('nome');
					$apelido = $request->post('apelido');
					$email = $request->post('email');
					// guardando os valores
					$usuario = new Usuarios();
					$usuario->nome = $nome;
					$usuario->apelido = $apelido;
					$usuario->email = $email;
					// inserindo no banco
					$inserindo = $usuario->save();
					if ($inserindo) {
						$app->flash('message', 'Usuário inserido com sucesso!');
					} else {
						$app->flash('error', 'Error ao inserir dados');
					}
				} catch (Exception $e) {
					$app->status(400);
					echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));	
				}
				$app->redirect('new');
			});

			/**
			* Update de dados
			*/
			$app->get('/update:id', function($id=0) use($app) {
				try {
					// obter usuário pelo ID
					$usuario = Usuarios::find($id);
					if ($usuario) {
						$result = json_decode($usuario, true);

						$nome = $result['nome'];
						$apelido = $result['apelido'];
						$email = $result['email'];
						
					} else {
						throw new Exception("Usuário não encontrado", 1);
					}
				} catch (Exception $e) {
					$app->status(400);
					echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
				}
				$app->render('editar.php', $result);
			})->name('update');
			// update de usuarios
			$app->post('/update:id', function($id) use($app) {
				// atualizar dados
				try {
					$id = (int)$id;
					// pegando dados do input
					$request = $app->request;
					$nome = $request->post('nome');
					$apelido = $request->post('apelido');
					$email = $request->post('email');
					// atualizando
					$atualizando = Usuarios::where('id', '=', $id)
									->limit(1)
									->update(array('nome' => $nome, 'apelido' => $apelido, 'email' => $email));
					if ($atualizando) {
						$app->flash('message', 'Atualizado com sucesso!');
					} else {
						$app->flash('error', 'Error ao atualizar dados');
					}
				} catch (Exception $e) {
					$app->status(400);
					echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
				}
				$redirecionar = $app->urlFor('update', array('id' => $id));
				$app->redirect($redirecionar);
			});
			
			/**
			* Delete de dados
			*/
			// delete de usuarios
			$app->get('/delete:id', function($id) use($app) {
				try {
					$id = (int)$id;
					// buscando e apagando
					$apagando = Usuarios::where('id', '=', $id)->delete();
					if ($apagando) {
						echo json_encode(array('status' => 'success', 'message' => 'Deletado corretamente'));
					} else {
						throw new Exception("Error ao deletar usuario", 1);
					}
				} catch (Exception $e) {
					$app->status(400);
					echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
				}
				$app->redirect($app->urlFor('all'));
			});
		});
	});








	/**
	* selecionando todos os usuarios
	*/
	// $app->get('/usuarios', function() use($app, $db) {
	// 	$dbquery = $db->prepare("SELECT * FROM usuarios");
	// 	$dbquery->execute();
	// 	$data['usuarios'] = $dbquery->fetchAll(PDO::FETCH_ASSOC);
	// 	$app->render('usuarios.php', $data);
	// 	/** Maneira simplificada
	// 	*	foreach ($dbquery as $fila) {
	// 	*		echo '-> '.$fila['nome'].'<br/>';
	// 	*	}
	// 	*/
	// })->name('usuarios');

	/**
	* novo usuario
	*/ 
	// $app->get('/novo-usuario', function() use($app) {
	// 	$app->render('novo.php');
	// });

	// $app->post('/novo-usuario', function() use($app, $db) {
	// 	$request = $app->request;
	// 	$nome = $request->post('nome');
	// 	$apelido = $request->post('apelido');
	// 	$email = $request->post('email');

	// 	// inserindo dados
	// 	$dbquery = $db->prepare("INSERT INTO usuarios(nome, apelido, email, alta) VALUES(:nome, :apelido, :email, NOW())");
	// 	$inserindo = $dbquery->execute(array(':nome' => $nome, ':apelido' => $apelido, ':email' => $email));
	// 	if ($inserindo) {
	// 		$app->flash('message', 'Usuário inserido corretamente.');
	// 	} else {
	// 		$app->flash('error', 'Houve um erro ao inserir um novo usuário.');
	// 	}
	// 	$app->redirect('novo-usuario');
	// });

	/**
	* editar usuario
	*/
	// $app->get('/editar-:id-usuario', function ($id=0) use($app, $db) {
	// 	$id = (int)$id; // força que seja um inteiro
	// 	// buscando usuario
	// 	$dbquery = $db->prepare("SELECT * FROM usuarios WHERE id=:id LIMIT 1");
	// 	$dbquery->execute(array(':id' => $id));
	// 	$result = $dbquery->fetch(PDO::FETCH_ASSOC);
	// 	if (!$result) {
	// 		$app->halt(404, 'Usuário não encontrado!');
	// 	}
	// 	$app->render('editar.php', $result);
	// })->name('editarusuario');

	// $app->post('/editar-:id-usuario', function ($id) use($app, $db) {
	// 	$id = (int)$id;
	// 	$request = $app->request;
	// 	$nome = $request->post('nome');
	// 	$apelido = $request->post('apelido');
	// 	$email = $request->post('email');
	// 	// atualizando dados do usuario
	// 	$dbquery = $db->prepare("UPDATE usuarios SET nome=:nome, apelido=:apelido, email=:email WHERE id=:id LIMIT 1");
	// 	$dbquery->execute(array(':nome' => $nome, ':apelido' => $apelido, ':email' => $email, ':id' => $id));
	// 	$app->flash('message', 'Usuário atualizado corretamente.');
	// 	$redirecionar = $app->urlFor('editarusuario', array('id' => $id));
	// 	$app->redirect($redirecionar);
	// });

	/*
	* deletar usuario
	*/
	// $app->get('/apagar-:id-usuario', function ($id) use($app, $db) {
	// 	$id = (int)$id;
	// 	$dbquery = $db->prepare("DELETE FROM usuarios WHERE id=:id LIMIT 1");
	// 	$dbquery->execute(array(':id' => $id));
	// 	$app->redirect($app->urlFor('usuarios'));
	// });

	// ----------------- exemplo
	/*$app->get('/hello/:name', function ($name) {
	    echo "Hello, ".$name;
	});

	$app->map('/home/:name', function ($name) use($app) {
		$data = array('name' => $name, 'age' => 21);
		$app->render('templates.php', $data);
	})->via('GET','POST')->conditions(array('name' => '[a-z]{3,}'))->name('inicio'); // condições de caracter

	$app->get('/chamada', function() use($app) {
		$url = 	$app->urlFor('inicio', array('name' => 'alonso'));
		echo '<a href="'.$url.'">Ir a home</a>';
	});

	*/

	$app->run();