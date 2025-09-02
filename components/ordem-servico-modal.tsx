"use client"

import { useState, useEffect } from 'react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { useAuth } from '@/contexts/auth-context'
import { useData } from '@/contexts/data-context'
import { generatePDF } from '@/utils/pdf-generator'
import { CheckCircle, XCircle, RotateCcw, Clock, FileText, Building, User, Wrench } from 'lucide-react'

interface OrdemServicoModalProps {
  isOpen: boolean
  onClose: () => void
  eventId: string | null
}

export default function OrdemServicoModal({ isOpen, onClose, eventId }: OrdemServicoModalProps) {
  const { users } = useAuth()
  const { events, getOrdemServicoByEventId, updateOrdemServico, updateEventStatus } = useData()
  const [ordemServico, setOrdemServico] = useState({
    // Dados da empresa/cliente
    cliente: '',
    cnpj: '',
    endereco_cliente: '',
    contato_cliente: '',
    designacao: '',
    // Horários
    hora_inicio: '',
    hora_fim: '',
    // Dados do equipamento
    modelo_equipamento: '',
    numero_equipamento: '',
    serial_equipamento: '',
    // Serviço
    descricao_servico: '',
    observacao: '',
    // Campos originais mantidos
    materiais: '',
    mac: '',
    observacoes: ''
  })

  const event = events.find(e => e.id === eventId)
  const responsavel = users.find(u => u.id === event?.responsavel_id)

  useEffect(() => {
    if (isOpen && eventId) {
      const os = getOrdemServicoByEventId(eventId)
      if (os) {
        setOrdemServico({
          cliente: os.cliente || '',
          cnpj: os.cnpj || '',
          endereco_cliente: os.endereco_cliente || '',
          contato_cliente: os.contato_cliente || '',
          designacao: os.designacao || '',
          hora_inicio: os.hora_inicio || '',
          hora_fim: os.hora_fim || '',
          modelo_equipamento: os.modelo_equipamento || '',
          numero_equipamento: os.numero_equipamento || '',
          serial_equipamento: os.serial_equipamento || '',
          descricao_servico: os.descricao_servico || '',
          observacao: os.observacao || '',
          materiais: os.materiais || '',
          mac: os.mac || '',
          observacoes: os.observacoes || ''
        })
      } else if (event) {
        // Preencher com dados do evento se não houver O.S.
        const eventDate = new Date(event.start)
        setOrdemServico(prev => ({
          ...prev,
          cliente: event.title,
          endereco_cliente: event.endereco,
          designacao: event.assunto,
          hora_inicio: eventDate.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }),
          descricao_servico: 'Ativação'
        }))
      }
    }
  }, [isOpen, eventId, getOrdemServicoByEventId, event])

  const handleSave = () => {
    if (eventId) {
      const os = getOrdemServicoByEventId(eventId)
      if (os) {
        updateOrdemServico(os.id, ordemServico)
      }
    }
  }

  const handleStatusChange = (newStatus: 'agendado' | 'feito' | 'reagendado' | 'cancelado') => {
    if (eventId) {
      updateEventStatus(eventId, newStatus)
    }
  }

  const handleGeneratePDF = () => {
    if (event && responsavel) {
      handleSave() // Salvar antes de gerar o PDF
      generatePDF(event, ordemServico, responsavel)
    }
  }

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'agendado': return 'text-blue-600 bg-blue-100 border-blue-200'
      case 'feito': return 'text-green-600 bg-green-100 border-green-200'
      case 'reagendado': return 'text-yellow-600 bg-yellow-100 border-yellow-200'
      case 'cancelado': return 'text-red-600 bg-red-100 border-red-200'
      default: return 'text-gray-600 bg-gray-100 border-gray-200'
    }
  }

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'agendado': return <Clock className="h-4 w-4" />
      case 'feito': return <CheckCircle className="h-4 w-4" />
      case 'reagendado': return <RotateCcw className="h-4 w-4" />
      case 'cancelado': return <XCircle className="h-4 w-4" />
      default: return <Clock className="h-4 w-4" />
    }
  }

  if (!event || !responsavel) return null

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="sm:max-w-[800px] max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle className="flex items-center space-x-2">
            <FileText className="h-5 w-5 text-blue-600" />
            <span>Ordem de Serviço - {event.title}</span>
          </DialogTitle>
        </DialogHeader>
        
        <div className="space-y-6">
          {/* Status do Evento */}
          <div className="bg-gray-50 p-4 rounded-lg">
            <div className="flex items-center justify-between mb-4">
              <h3 className="font-semibold text-gray-900">Status do Evento</h3>
              <div className={`flex items-center space-x-2 px-3 py-1 rounded-full border ${getStatusColor(event.status)}`}>
                {getStatusIcon(event.status)}
                <span className="font-medium capitalize">{event.status}</span>
              </div>
            </div>
            
            <div className="grid grid-cols-2 md:grid-cols-4 gap-2">
              <Button
                size="sm"
                variant={event.status === 'agendado' ? 'default' : 'outline'}
                onClick={() => handleStatusChange('agendado')}
                className="flex items-center space-x-1"
              >
                <Clock className="h-4 w-4" />
                <span>Agendado</span>
              </Button>
              <Button
                size="sm"
                variant={event.status === 'feito' ? 'default' : 'outline'}
                onClick={() => handleStatusChange('feito')}
                className="flex items-center space-x-1 bg-green-600 hover:bg-green-700 text-white"
              >
                <CheckCircle className="h-4 w-4" />
                <span>Feito</span>
              </Button>
              <Button
                size="sm"
                variant={event.status === 'reagendado' ? 'default' : 'outline'}
                onClick={() => handleStatusChange('reagendado')}
                className="flex items-center space-x-1 bg-yellow-600 hover:bg-yellow-700 text-white"
              >
                <RotateCcw className="h-4 w-4" />
                <span>Reagendar</span>
              </Button>
              <Button
                size="sm"
                variant={event.status === 'cancelado' ? 'default' : 'outline'}
                onClick={() => handleStatusChange('cancelado')}
                className="flex items-center space-x-1 bg-red-600 hover:bg-red-700 text-white"
              >
                <XCircle className="h-4 w-4" />
                <span>Cancelar</span>
              </Button>
            </div>
          </div>

          {/* Dados do Cliente */}
          <div className="border rounded-lg p-4">
            <h3 className="font-semibold text-gray-900 mb-4 flex items-center space-x-2">
              <Building className="h-5 w-5" />
              <span>Dados do Cliente</span>
            </h3>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="cliente">Cliente</Label>
                <Input
                  id="cliente"
                  value={ordemServico.cliente}
                  onChange={(e) => setOrdemServico(prev => ({ ...prev, cliente: e.target.value }))}
                  placeholder="Nome do cliente"
                />
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="cnpj">CNPJ</Label>
                <Input
                  id="cnpj"
                  value={ordemServico.cnpj}
                  onChange={(e) => setOrdemServico(prev => ({ ...prev, cnpj: e.target.value }))}
                  placeholder="00.000.000/0001-00"
                />
              </div>
              
              <div className="space-y-2 md:col-span-2">
                <Label htmlFor="endereco_cliente">Endereço</Label>
                <Input
                  id="endereco_cliente"
                  value={ordemServico.endereco_cliente}
                  onChange={(e) => setOrdemServico(prev => ({ ...prev, endereco_cliente: e.target.value }))}
                  placeholder="Endereço completo do cliente"
                />
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="contato_cliente">Contato</Label>
                <Input
                  id="contato_cliente"
                  value={ordemServico.contato_cliente}
                  onChange={(e) => setOrdemServico(prev => ({ ...prev, contato_cliente: e.target.value }))}
                  placeholder="Nome - (00) 00000-0000"
                />
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="designacao">Designação</Label>
                <Input
                  id="designacao"
                  value={ordemServico.designacao}
                  onChange={(e) => setOrdemServico(prev => ({ ...prev, designacao: e.target.value }))}
                  placeholder="Descrição da designação"
                />
              </div>
            </div>
          </div>

          {/* Horários */}
          <div className="border rounded-lg p-4">
            <h3 className="font-semibold text-gray-900 mb-4 flex items-center space-x-2">
              <Clock className="h-5 w-5" />
              <span>Horários</span>
            </h3>
            
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="hora_inicio">Hora Início</Label>
                <Input
                  id="hora_inicio"
                  type="time"
                  value={ordemServico.hora_inicio}
                  onChange={(e) => setOrdemServico(prev => ({ ...prev, hora_inicio: e.target.value }))}
                />
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="hora_fim">Hora Fim</Label>
                <Input
                  id="hora_fim"
                  type="time"
                  value={ordemServico.hora_fim}
                  onChange={(e) => setOrdemServico(prev => ({ ...prev, hora_fim: e.target.value }))}
                />
              </div>
            </div>
          </div>

          {/* Dados do Equipamento */}
          <div className="border rounded-lg p-4">
            <h3 className="font-semibold text-gray-900 mb-4 flex items-center space-x-2">
              <Wrench className="h-5 w-5" />
              <span>Dados do Equipamento</span>
            </h3>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div className="space-y-2">
                <Label htmlFor="modelo_equipamento">Modelo</Label>
                <Input
                  id="modelo_equipamento"
                  value={ordemServico.modelo_equipamento}
                  onChange={(e) => setOrdemServico(prev => ({ ...prev, modelo_equipamento: e.target.value }))}
                  placeholder="DM985-422"
                />
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="numero_equipamento">Número</Label>
                <Input
                  id="numero_equipamento"
                  value={ordemServico.numero_equipamento}
                  onChange={(e) => setOrdemServico(prev => ({ ...prev, numero_equipamento: e.target.value }))}
                  placeholder="Número do equipamento"
                />
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="serial_equipamento">Serial</Label>
                <Input
                  id="serial_equipamento"
                  value={ordemServico.serial_equipamento}
                  onChange={(e) => setOrdemServico(prev => ({ ...prev, serial_equipamento: e.target.value }))}
                  placeholder="HW3DACM91913F58"
                />
              </div>
            </div>
          </div>

          {/* Descrição do Serviço */}
          <div className="border rounded-lg p-4">
            <h3 className="font-semibold text-gray-900 mb-4 flex items-center space-x-2">
              <FileText className="h-5 w-5" />
              <span>Descrição do Serviço</span>
            </h3>
            
            <div className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="descricao_servico">Descrição</Label>
                <Textarea
                  id="descricao_servico"
                  value={ordemServico.descricao_servico}
                  onChange={(e) => setOrdemServico(prev => ({ ...prev, descricao_servico: e.target.value }))}
                  placeholder="Descreva o serviço realizado"
                  rows={3}
                />
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="observacao">Observação</Label>
                <Textarea
                  id="observacao"
                  value={ordemServico.observacao}
                  onChange={(e) => setOrdemServico(prev => ({ ...prev, observacao: e.target.value }))}
                  placeholder="Observações adicionais"
                  rows={3}
                />
              </div>
            </div>
          </div>
          
          <div className="flex justify-end space-x-2 pt-4 border-t">
            <Button type="button" variant="outline" onClick={onClose}>
              Fechar
            </Button>
            <Button type="button" onClick={handleSave}>
              Salvar
            </Button>
            <Button type="button" onClick={handleGeneratePDF} className="bg-red-600 hover:bg-red-700">
              Gerar PDF
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  )
}
