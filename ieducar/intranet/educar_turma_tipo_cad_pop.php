<?php

use App\Models\LegacySchoolClassType;

return new class extends clsCadastro {
    public $pessoa_logada;
    public $cod_turma_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $sgl_tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;
    public $ref_cod_escola;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_turma_tipo=$_GET['cod_turma_tipo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(570, $this->pessoa_logada, 7, 'educar_turma_tipo_lst.php');

        if (is_numeric($this->cod_turma_tipo)) {
            $registro = LegacySchoolClassType::findOrFail($this->cod_turma_tipo)->toArray();
            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(570, $this->pessoa_logada, 7);
                $retorno = 'Editar';
            }
        }
        $this->nome_url_cancelar = 'Cancelar';
        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_turma_tipo', $this->cod_turma_tipo);

        if ($_GET['precisa_lista']) {
            $obrigatorio = true;
            // foreign keys
            $get_escola = false;
            include('include/pmieducar/educar_campo_lista.php');
        } else {
            $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        }
        // text
        $this->campoTexto('nm_tipo', 'Turma Tipo', $this->nm_tipo, 30, 255, true);
        $this->campoTexto('sgl_tipo', 'Sigla', $this->sgl_tipo, 15, 15, true);
    }

    public function Novo()
    {
        $classType = new LegacySchoolClassType();
        $classType->ref_usuario_cad = $this->pessoa_logada;
        $classType->nm_tipo = $this->nm_tipo;
        $classType->sgl_tipo = $this->sgl_tipo;
        $classType->data_cadastro = now();
        if (is_numeric($this->ref_cod_instituicao)) {
            $classType->ref_cod_instituicao = $this->ref_cod_instituicao;
        }
        $classType->ativo = 1;

        if ($classType->save()) {
            echo "<script>
                        if (parent.document.getElementById('ref_cod_turma_tipo').disabled)
                            parent.document.getElementById('ref_cod_turma_tipo').options[0] = new Option('Selectione um tipo de turma', '', false, false);
                        parent.document.getElementById('ref_cod_turma_tipo').options[parent.document.getElementById('ref_cod_turma_tipo').options.length] = new Option('$this->nm_tipo', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_turma_tipo').value = '$cadastrou';
                        parent.document.getElementById('ref_cod_turma_tipo').disabled = false;
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();

            return true;
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
    }

    public function Excluir()
    {
    }

    public function makeExtra()
    {
        if (! $_GET['precisa_lista']) {
            return file_get_contents(__DIR__ . '/scripts/extra/educar-habilitacao-cad-pop.js');
        }

        return '';
    }

    public function Formular()
    {
        $this->title = 'Turma Tipo';
        $this->processoAp = '570';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
