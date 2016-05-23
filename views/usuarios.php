<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Usuarios</title>
	<link rel="stylesheet" href="../../views/css/semantic.min.css" />
	<link rel="stylesheet" href="../../views/css/icon.min.css" />
</head>
<body>

	<div class="ui raised very padded text container segment">
		<h2 class="ui header">Usuários</h2>
		<?php foreach ($data as $key => $value): ?>
			<div class="ui grid">
				<div class="ten wide column"><?php echo $value['nome'].' <b>'.$value['apelido'].'</b>'; ?></div>
				<div class="six wide column">
					<a class="ui left attached inverted olive button" href="update<?php echo $value['id'] ?>">Editar</a>
					<a class="right attached ui inverted red button" href="delete<?php echo $value['id'] ?>">Apagar</a>
				</div>
			</div>
		<?php endforeach; ?>
		<a class="positive ui button" href="new">Novo usuário</a>
	</div>

	<script src="../../views/js/jquery-2.2.3.min.js"></script>
	<script src="../../views/js/semantic.min.js"></script>
</body>
</html>