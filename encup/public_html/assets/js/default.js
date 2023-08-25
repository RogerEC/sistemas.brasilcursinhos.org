$(function() {

    var optionsAline = {
        html: true,
        title: "Aline dos Santos Akimoto",
        content: '<div class="d-flex justify-content-center"><div class="card overflow-hidden mb-3 w-75"><img src="/assets/img/convidados/aline-min.png" class="w-100" alt="Aline"></div></div><p class="mb-1">Psicóloga graduada pela universidade São Judas Tadeu em 2012 (graças ao Prouni e Fies)</p><p class="mb-1">Tem aprimoramento em Psicologia e Intervenção escolar pela mesma instituição</p><p class="mb-1">Especialista em Terapia Cognitivo Comportamental</p><p class="mb-1">Psicopedagoga clínica e institucional</p><p class="mb-1">Responsável pelo serviço de Apoio Psicológico do cursinho Each USP desde 2020</p><p class="mb-1">Psicóloga voluntária no coletivo Prateleira de Quadrinhos desde 2021</p><p class="mb-0">Atua como psicóloga clínica em consultório particular.</p>'
    }

    var optionsVitor = {
        html: true,
        title: "Vitor Diego Ramos",
        content: '<div class="d-flex justify-content-center"><div class="card overflow-hidden mb-3 w-75"><img src="/assets/img/convidados/vitor-min.png" class="w-100" alt="Vitor"></div></div><p class="mb-0">Engenheiro de Dados na consultoria Apllos, especializado em Big Data e Analytics, com ênfase em Databricks e ambientes de nuvem como Google Cloud e AWS. Sua carreira de 15 anos na gestão evoluiu para a área de dados, destacando-se tanto em habilidades técnicas quanto em liderança e gestão de projetos. Possui certificações diversas nível Professional e Associate em Google Cloud, AWS e Databricks. É ativo na educação, atuando como professor, tutor, e instrutor em diversas instituições - Escola Korú, The Plumbers, Engenharia de Dados Academy, etc. Fora do trabalho, ele está envolvido em projetos voluntários, como Diretor de Dados na Brasil Cursinhos e Coordenador de Trilha de Capacitação Google Cloud no Canal da Cloud, focando na promoção da educação e impacto social positivo.</p>'
    }

    var optionsLilian = {
        html: true,
        title: "Lilian Moreira",
        content: '<div class="d-flex justify-content-center"><div class="card overflow-hidden mb-3 w-75"><img src="/assets/img/convidados/lilian-min.png" class="w-100" alt="Vitor"></div></div><p class="mb-0">Licenciada em Química pela Universidade Federal da Bahia, com mestrado em andamento no Programa de Pós-Graduação em Ensino e História das Ciências e da Matemática da Universidade Federal do ABC. Possui experiência profissional na educação básica, nível superior, técnico e em comissão antidiscriminação.</p>'
    }

    var optionsGabriel = {
        html: true,
        title: "Gabriel Saraiva Gomes",
        content: '<div class="d-flex justify-content-center"><div class="card overflow-hidden mb-3 w-75"><img src="/assets/img/convidados/gabriel-min.png" class="w-100" alt="Vitor"></div></div><p class="mb-0">Mestre em Educação e Licenciado em Química pela Universidade de São Paulo, com pesquisas voltadas ao planejamento de ensino por meio de temas sociocientíficos e da articulação entre aspectos discursivos e estruturais das questões-problema para ensino de Química. Ex-professor do Cursinho Popular Transformação e do Cursinho Popular da EACH-USP, atualmente professor da rede privada na cidade de São Paulo (SP).</p>'
    }

    var optionsIgor = {
        html: true,
        title: "Igor Moura Guilherme",
        content: '<div class="d-flex justify-content-center"><div class="card overflow-hidden mb-3 w-75"><img src="/assets/img/convidados/igor.jpeg" class="w-100" alt="Arco Instituto"></div></div><p class="mb-0">Líder do Arco Instituto, negócio do grupo Arco Educação que oferece soluções educacionais para jovens em situação de vulnerabilidade social. Engenheiro de Alimentos pela Universidade Federal do Ceará, trabalha há 7 anos com educação, tendo passado pelo terceiro setor e consultoria.</p>'
    }

    var optionsPriscila = {
        html: true,
        title: "Priscila Martins Baia",
        content: '<div class="d-flex justify-content-center"><div class="card overflow-hidden mb-3 w-75"><img src="/assets/img/convidados/priscila.jpeg" class="w-100" alt="Arco Instituto"></div></div><p class="mb-0">Consultora pedagógica do Arco Instituto, liderando a produção de conteúdo, implantações, formações e acompanhamento das nossas soluções educacionais com as organizações parceiras. Alumni do Programa de Desenvolvimento de Lideranças do Ensina Brasil, mestra em Ciências Humanas e Sociais Aplicadas, bacharela em Administração e Gestão de Políticas Públicas pela UNICAMP.</p>'
    }

    var popoverAline = new bootstrap.Popover($("#btn-aline"), optionsAline);
    var popoverVitor = new bootstrap.Popover($("#btn-vitor"), optionsVitor);
    var popoverLilian = new bootstrap.Popover($("#btn-lilian"), optionsLilian);
    var popoverGabriel = new bootstrap.Popover($("#btn-gabriel"), optionsGabriel);
    var popoverPriscila = new bootstrap.Popover($("#btn-priscila"), optionsPriscila);
    var popoverIgor = new bootstrap.Popover($("#btn-igor"), optionsIgor);
    var popoverAline2 = new bootstrap.Popover($("#btn-aline2"), optionsAline);
    var popoverVitor2 = new bootstrap.Popover($("#btn-vitor2"), optionsVitor);
    var popoverLilian2 = new bootstrap.Popover($("#btn-lilian2"), optionsLilian);
    var popoverGabriel2 = new bootstrap.Popover($("#btn-gabriel2"), optionsGabriel);
    var popoverPriscila2 = new bootstrap.Popover($("#btn-priscila2"), optionsPriscila);
    var popoverIgor2 = new bootstrap.Popover($("#btn-igor2"), optionsIgor);
    
    $(".btn-expand-sidebar").on("click", function(){
        if($(".btn-expand-sidebar").hasClass("collapsed")) {
            $(".main-content").removeClass("d-none");
        } else {
            $(".main-content").addClass("d-none");
        }
    });

    $("#btn-page-back-ex").on("click", function(){
        if($("#tickets").hasClass('d-none')) {
            $("#pix").addClass("d-none");
            $("#tickets").removeClass("d-none");
        } else {
            window.location = '/';
        }
    });

    $(".btn-pay-with-pix").on("click", function(){
        $("#tickets").addClass("d-none");
        $("#pix").removeClass("d-none");
        $(".code-pix").hide();
        $("#code-pix-"+$(this).val()).show();
    });

    $(".code-pix-copy-paste").on("click", function(){
        navigator.clipboard.writeText($(this).val());
        $(".alert-pix").remove();
        $(this).parent().after('<div class="alert alert-success alert-dismissible fade show alert-pix" role="alert">O Código PIX foi copiado para área de transferência!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
    });

    $("#btn-schedule-page-back").on("click", function(){
        if($("#schedule").hasClass('d-none')) {
            $("#workshops").addClass('d-none');
            $("#table-1").addClass('d-none');
            $("#table-2").addClass('d-none');
            $("#table-3").addClass('d-none');
            $("#schedule").removeClass("d-none");
        } else {
            $("#btn-close-modal-event-schedule").trigger("click");
        }
    });

    $("#btn-workshop-info").on("click", function(){
        $("#schedule").addClass("d-none");
        $("#workshops").removeClass("d-none");
    });

    $("#btn-table1-info").on("click", function(){
        $("#schedule").addClass("d-none");
        $("#table-1").removeClass("d-none");
    });

    $("#btn-table2-info").on("click", function(){
        $("#schedule").addClass("d-none");
        $("#table-2").removeClass("d-none");
    })

    $("#btn-table3-info").on("click", function(){
        $("#schedule").addClass("d-none");
        $("#table-3").removeClass("d-none");
    });

    $("#btn-close-modal-event-schedule").on("click", function(){
        $("#workshops").addClass('d-none');
        $("#table-1").addClass('d-none');
        $("#table-2").addClass('d-none');
        $("#table-3").addClass('d-none');
        $("#schedule").removeClass("d-none");
    });
});
