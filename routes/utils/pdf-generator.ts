import jsPDF from 'jspdf'
import { Event, OrdemServico, User } from '@/types'

export function generatePDF(event: Event, ordemServico: OrdemServico, responsavel: User) {
  const doc = new jsPDF()
  
  // Configurações
  const pageWidth = doc.internal.pageSize.width
  const pageHeight = doc.internal.pageSize.height
  const margin = 15
  let yPosition = 20

  // Função para adicionar linha horizontal
  const addLine = (y: number, startX = margin, endX = pageWidth - margin) => {
    doc.line(startX, y, endX, y)
  }

  // Função para adicionar texto centralizado
  const addCenteredText = (text: string, y: number, fontSize = 12) => {
    doc.setFontSize(fontSize)
    const textWidth = doc.getTextWidth(text)
    doc.text(text, (pageWidth - textWidth) / 2, y)
  }

  // Cabeçalho
  doc.setFontSize(16)
  doc.setFont('helvetica', 'bold')
  addCenteredText('ORDEM DE SERVIÇO (O.S.)', yPosition)
  yPosition += 15

  // Protocolo e dados principais
  doc.setFontSize(10)
  doc.setFont('helvetica', 'normal')
  
  // Linha 1: Protocolo, Cliente, CNPJ
  const protocoloNum = `FPO01.02-01 CSLP-${new Date().getFullYear()}${String(new Date().getMonth() + 1).padStart(2, '0')}${String(Date.now()).slice(-4)}`
  doc.text(`Protocolo: ${protocoloNum}`, margin, yPosition)
  yPosition += 6
  
  doc.setFont('helvetica', 'bold')
  doc.text('CLIENTE:', margin, yPosition)
  doc.setFont('helvetica', 'normal')
  doc.text(ordemServico.cliente || event.title, margin + 20, yPosition)
  yPosition += 6
  
  doc.setFont('helvetica', 'bold')
  doc.text('CNPJ:', margin, yPosition)
  doc.setFont('helvetica', 'normal')
  doc.text(ordemServico.cnpj || '00.000.000/0001-00', margin + 15, yPosition)
  yPosition += 6
  
  doc.setFont('helvetica', 'bold')
  doc.text('ENDEREÇO:', margin, yPosition)
  doc.setFont('helvetica', 'normal')
  doc.text(ordemServico.endereco_cliente || event.endereco, margin + 25, yPosition)
  yPosition += 6
  
  doc.setFont('helvetica', 'bold')
  doc.text('CONTATO:', margin, yPosition)
  doc.setFont('helvetica', 'normal')
  doc.text(ordemServico.contato_cliente || 'Não informado', margin + 22, yPosition)
  yPosition += 6
  
  doc.setFont('helvetica', 'bold')
  doc.text('DESIGNAÇÃO:', margin, yPosition)
  doc.setFont('helvetica', 'normal')
  doc.text(ordemServico.designacao || event.assunto, margin + 30, yPosition)
  yPosition += 10

  // Data e horários
  const eventDate = new Date(event.start)
  doc.setFont('helvetica', 'bold')
  doc.text('DATA DE EMISSÃO:', margin, yPosition)
  doc.setFont('helvetica', 'normal')
  doc.text(eventDate.toLocaleDateString('pt-BR'), margin + 40, yPosition)
  yPosition += 6
  
  doc.setFont('helvetica', 'bold')
  doc.text('HORA INÍCIO:', margin, yPosition)
  doc.setFont('helvetica', 'normal')
  doc.text(ordemServico.hora_inicio || eventDate.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }), margin + 30, yPosition)
  
  doc.setFont('helvetica', 'bold')
  doc.text('HORA FIM:', margin + 80, yPosition)
  doc.setFont('helvetica', 'normal')
  doc.text(ordemServico.hora_fim || '__:__', margin + 105, yPosition)
  yPosition += 15

  // Linha separadora
  addLine(yPosition)
  yPosition += 10

  // Dados do Equipamento
  doc.setFontSize(12)
  doc.setFont('helvetica', 'bold')
  doc.text('Dados do Equipamento', margin, yPosition)
  yPosition += 8

  doc.setFontSize(10)
  doc.setFont('helvetica', 'bold')
  doc.text('Modelo:', margin, yPosition)
  doc.setFont('helvetica', 'normal')
  doc.text(ordemServico.modelo_equipamento || 'DM985-422', margin + 20, yPosition)
  yPosition += 6

  doc.setFont('helvetica', 'bold')
  doc.text('Nº:', margin, yPosition)
  doc.setFont('helvetica', 'normal')
  doc.text(ordemServico.numero_equipamento || 'Não informado', margin + 12, yPosition)
  yPosition += 6

  doc.setFont('helvetica', 'bold')
  doc.text('Serial:', margin, yPosition)
  doc.setFont('helvetica', 'normal')
  doc.text(ordemServico.serial_equipamento || 'HW3DACM91913F58', margin + 18, yPosition)
  yPosition += 15

  // Linha separadora
  addLine(yPosition)
  yPosition += 10

  // Contratante/Representante
  doc.setFontSize(12)
  doc.setFont('helvetica', 'bold')
  doc.text('CONTRATANTE / REPRESENTANTE', margin, yPosition)
  yPosition += 8

  doc.setFontSize(10)
  doc.setFont('helvetica', 'normal')
  const contratanteText = ordemServico.contato_cliente || `${responsavel.nome} ${responsavel.sobrenome}`
  doc.text(contratanteText, margin, yPosition)
  yPosition += 15

  // Descrição do serviço
  doc.setFontSize(12)
  doc.setFont('helvetica', 'bold')
  doc.text('Descrição do serviço', margin, yPosition)
  yPosition += 8

  doc.setFontSize(10)
  doc.setFont('helvetica', 'normal')
  const descricaoText = ordemServico.descricao_servico || ordemServico.observacoes || 'Ativação'
  const descricaoLines = doc.splitTextToSize(descricaoText, pageWidth - 2 * margin)
  doc.text(descricaoLines, margin, yPosition)
  yPosition += descricaoLines.length * 5 + 10

  // Observação
  if (ordemServico.observacao || ordemServico.materiais) {
    doc.setFont('helvetica', 'bold')
    doc.text('Observação', margin, yPosition)
    yPosition += 6

    doc.setFont('helvetica', 'normal')
    const observacaoText = ordemServico.observacao || ordemServico.materiais || ''
    const observacaoLines = doc.splitTextToSize(observacaoText, pageWidth - 2 * margin)
    doc.text(observacaoLines, margin, yPosition)
    yPosition += observacaoLines.length * 5 + 15
  }

  // Garantir espaço para assinaturas
  if (yPosition > pageHeight - 60) {
    doc.addPage()
    yPosition = 30
  }

  // Linha separadora antes das assinaturas
  addLine(yPosition)
  yPosition += 15

  // Assinaturas
  doc.setFontSize(12)
  doc.setFont('helvetica', 'bold')
  addCenteredText('Assinaturas', yPosition)
  yPosition += 20

  // Linha para assinatura do técnico
  const leftSignatureX = margin
  const rightSignatureX = pageWidth / 2 + 10
  const signatureWidth = (pageWidth / 2) - margin - 10

  doc.line(leftSignatureX, yPosition, leftSignatureX + signatureWidth, yPosition)
  doc.setFontSize(10)
  doc.setFont('helvetica', 'normal')
  doc.text('TÉCNICO NOC RESPONSÁVEL', leftSignatureX, yPosition + 8)
  doc.text(`${responsavel.nome} ${responsavel.sobrenome}`, leftSignatureX, yPosition + 15)

  // Linha para assinatura do cliente
  doc.line(rightSignatureX, yPosition, rightSignatureX + signatureWidth, yPosition)
  doc.text('CLIENTE / RESPONSÁVEL', rightSignatureX, yPosition + 8)

  // Rodapé com data e hora de geração
  doc.setFontSize(8)
  doc.setFont('helvetica', 'italic')
  const footerText = `Documento gerado em ${new Date().toLocaleString('pt-BR')}`
  doc.text(footerText, margin, pageHeight - 10)

  // Salvar o PDF
  const fileName = `OS_${protocoloNum.replace(/\s+/g, '_')}_${new Date().toISOString().split('T')[0]}.pdf`
  doc.save(fileName)
}
