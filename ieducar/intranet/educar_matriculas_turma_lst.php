<?php

require_once('include/clsBase.inc.php');
require_once('include/clsListagem.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Matr&iacute;culas Turmas");
        $this->processoAp = '659';
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsListagem
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $ref_cod_turma;

    public $ref_ref_cod_serie;

    public $ref_cod_escola;

    public $ref_ref_cod_escola;

    public $ref_cod_instituicao;

    public $ref_cod_curso;

    public $ref_cod_serie;

    public $ano;

    public function Gerar()
    {
        $this->titulo = 'Matrículas Turma - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Ano',
            'Turma',
            'Série',
            'Curso',
            'Escola'
        ];

        $this->addCabecalhos($lista_busca);

        $this->inputsHelper()->dynamic(['ano', 'instituicao'], ['required' => true]);
        $this->inputsHelper()->dynamic(['escola', 'curso', 'serie', 'turma'], ['required' => false]);

        if ($this->ref_cod_escola) {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $obj_turma = new clsPmieducarTurma();
        $obj_turma->setOrderby('nm_turma ASC');
        $obj_turma->setLimite($this->limite, $this->offset);
        if (!$this->ano) {
            $this->ano = date(Y);
        }

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_turma->codUsuario = $this->pessoa_logada;
        }

        $lista = $obj_turma->lista3(
            $this->ref_cod_turma,
            null,
            null,
            $this->ref_cod_serie,
            $this->ref_ref_cod_escola,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->ref_cod_curso,
            $this->ref_cod_instituicao,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->ano
        );

        $total = $obj_turma->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                if (class_exists('clsPmieducarEscola')) {
                    $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
                    $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                    $registro['nm_escola'] = $det_ref_cod_escola['nome'];
                } else {
                    $registro['ref_ref_cod_escola'] = 'Erro na gera&ccedil;&atilde;o';
                    echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
                }

                $lista_busca = [
                    "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">{$registro['ano']}</a>",
                    "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">{$registro['nm_turma']}</a>"
                ];

                if ($registro['ref_ref_cod_serie']) {
                    $lista_busca[] = "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">{$registro['nm_serie']}</a>";
                } else {
                    $lista_busca[] = "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">-</a>";
                }

                $lista_busca[] = "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">{$registro['nm_curso']}</a>";

                if ($registro['ref_ref_cod_escola']) {
                    $lista_busca[] = "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">{$registro['nm_escola']}</a>";
                } else {
                    $lista_busca[] = "<a href=\"educar_matriculas_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">-</a>";
                }

                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_matriculas_turma_lst.php', $total, $_GET, $this->nome, $this->limite);
        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'In&iacute;cio',
            'educar_index.php' => 'Escola',
            '' => 'Listagem de turmas para enturma&ccedil;&otilde;es'
        ]);
        $this->enviaLocalizacao($localizacao->montar());
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
?>

<script>

    document.getElementById('ref_cod_escola').onchange = function () {
        getEscolaCurso();
    }

    document.getElementById('ref_cod_curso').onchange = function () {
        getEscolaCursoSerie();
    }

    document.getElementById('ref_ref_cod_serie').onchange = function () {
        getTurma();
    }

</script>
