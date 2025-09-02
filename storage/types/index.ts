export interface User {
  id: string
  nome: string
  sobrenome: string
  email: string
  senha: string
}

export interface Event {
  id: string
  title: string
  assunto: string
  endereco: string
  start: string
  responsavel_id: string
  status: 'agendado' | 'feito' | 'reagendado' | 'cancelado'
  created_at: string
}

export interface OrdemServico {
  id: string
  evento_id: string
  // Dados da empresa/cliente
  cliente: string
  cnpj: string
  endereco_cliente: string
  contato_cliente: string
  designacao: string
  // Horários
  hora_inicio: string
  hora_fim: string
  // Dados do equipamento
  modelo_equipamento: string
  numero_equipamento: string
  serial_equipamento: string
  // Serviço
  descricao_servico: string
  observacao: string
  // Campos originais mantidos para compatibilidade
  materiais: string
  mac: string
  observacoes: string
}
