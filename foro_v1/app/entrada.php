<form method="POST">
    <table>
        <tr>
            <td>Nombre:</td>
            <td><input type="text" name="nombre" value="<?= htmlspecialchars($_REQUEST['nombre'] ?? '') ?>"></td>
        </tr>
        <tr>
            <td>Contraseña:</td>
            <td><input type="password" name="contraseña"></td>
        </tr>
    </table>
    <input type="submit" name="orden" value="Entrar">
</form>
