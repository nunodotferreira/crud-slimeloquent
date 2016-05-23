<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Editar usuario</title>
	<link rel="stylesheet" href="../../views/css/semantic.min.css" />
	<link rel="stylesheet" href="../../views/css/icon.min.css" />
</head>
<body>
	
	<div class="ui raised very padded text container segment">
		<form action="" method="POST" role="form" class="ui form">
			<h4 class="ui dividing header">Editar usuário</h4>
			<?php if (isset($flash['error'])): ?>
				<p class=""><?php echo $flash['error']; ?></p>
			<?php endif; ?>
			<div>
				<div class="two fields">
					<div class="field">
						<label for="nome">Nome</label>
						<input type="text" id="nome" name="nome" placeholder="Nome" value="<?php echo $nome; ?>" />
					</div>
					<div class="field">
						<label for="apelido">Sobrenome</label>
						<input type="text" id="apelido" name="apelido" placeholder="Sobrenome" value="<?php echo $apelido; ?>" />
					</div>
				</div>
				<label for="email">email</label>
				<input type="email" id="email" name="email" placeholder="example@email.com" value="<?php echo $email; ?>" />
			</div>
			<div class="ui success message">
				<?php if (isset($flash['message'])): ?>
						<i class="close icon"></i>
						<div class="header">Successo!</div>
						<p><?php echo $flash['message']; ?></p>
				<?php endif; ?>
			</div>
			<br/>
			<div class="right floated ui buttons">
				<a class="ui button" href="all">Cancelar</a>
				<div class="or" data-text="ou"></div>
				<button type="submit" class="ui positive button">Salvar</button>
			</div>
		</form>
	</div>


	<script src="../../views/js/jquery-2.2.3.min.js"></script>
	<script src="../../views/js/semantic.min.js"></script>
</body>
</html>