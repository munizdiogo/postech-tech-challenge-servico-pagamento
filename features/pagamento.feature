Feature: Cadastrar pagamento

    Scenario: Cadastrar pagamento com dados válidos
        Given que existem dados válidos para cadastrar um pagamento
        When eu chamar a função cadastrar pagamento
        Then eu devo receber uma confirmação de que os dados foram salvos com sucesso

    Scenario: Cadastrar pagamento com campo obrigatório não informado
        Given que existem dados inválidos para cadastrar um pagamento
        When eu chamar a função cadastrar pagamento com dados inválidos
        Then eu devo receber uma resposta de que o campo obrigatório para criar o pagamento não foi informado