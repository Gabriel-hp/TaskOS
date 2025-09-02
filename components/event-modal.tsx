"use client"

import { useState, useEffect } from 'react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { useAuth } from '@/contexts/auth-context'
import { useData } from '@/contexts/data-context'
import { Calendar, Clock, MapPin, User } from 'lucide-react'

interface EventModalProps {
  isOpen: boolean
  onClose: () => void
  selectedDate: string | null
}

export default function EventModal({ isOpen, onClose, selectedDate }: EventModalProps) {
  const { users } = useAuth()
  const { addEvent } = useData()
  const [formData, setFormData] = useState({
    title: '',
    assunto: '',
    endereco: '',
    horario: '09:00',
    responsavel_id: ''
  })
  const [isSubmitting, setIsSubmitting] = useState(false)

  useEffect(() => {
    if (isOpen) {
      setFormData({
        title: '',
        assunto: '',
        endereco: '',
        horario: '09:00',
        responsavel_id: ''
      })
    }
  }, [isOpen])

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    
    if (!selectedDate) return

    setIsSubmitting(true)

    try {
      const dateTime = `${selectedDate}T${formData.horario}:00`
      
      addEvent({
        title: formData.title,
        assunto: formData.assunto,
        endereco: formData.endereco,
        start: dateTime,
        responsavel_id: formData.responsavel_id
      })

      onClose()
    } catch (error) {
      console.error('Erro ao criar evento:', error)
    } finally {
      setIsSubmitting(false)
    }
  }

  const formatSelectedDate = (dateStr: string) => {
    const date = new Date(dateStr + 'T00:00:00')
    return date.toLocaleDateString('pt-BR', { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    })
  }

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="sm:max-w-[500px]">
        <DialogHeader>
          <DialogTitle className="flex items-center space-x-2">
            <Calendar className="h-5 w-5 text-blue-600" />
            <span>Novo Evento</span>
          </DialogTitle>
          {selectedDate && (
            <p className="text-sm text-gray-600 mt-2">
              {formatSelectedDate(selectedDate)}
            </p>
          )}
        </DialogHeader>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="title" className="flex items-center space-x-1">
              <span>Título</span>
              <span className="text-red-500">*</span>
            </Label>
            <Input
              id="title"
              value={formData.title}
              onChange={(e) => setFormData(prev => ({ ...prev, title: e.target.value }))}
              placeholder="Digite o título do evento"
              required
            />
          </div>
          
          <div className="space-y-2">
            <Label htmlFor="assunto" className="flex items-center space-x-1">
              <span>Assunto</span>
              <span className="text-red-500">*</span>
            </Label>
            <Textarea
              id="assunto"
              value={formData.assunto}
              onChange={(e) => setFormData(prev => ({ ...prev, assunto: e.target.value }))}
              placeholder="Descreva o assunto do evento"
              rows={3}
              required
            />
          </div>
          
          <div className="space-y-2">
            <Label htmlFor="endereco" className="flex items-center space-x-1">
              <MapPin className="h-4 w-4" />
              <span>Endereço</span>
              <span className="text-red-500">*</span>
            </Label>
            <Input
              id="endereco"
              value={formData.endereco}
              onChange={(e) => setFormData(prev => ({ ...prev, endereco: e.target.value }))}
              placeholder="Digite o endereço completo"
              required
            />
          </div>
          
          <div className="space-y-2">
            <Label htmlFor="horario" className="flex items-center space-x-1">
              <Clock className="h-4 w-4" />
              <span>Horário</span>
              <span className="text-red-500">*</span>
            </Label>
            <Input
              id="horario"
              type="time"
              value={formData.horario}
              onChange={(e) => setFormData(prev => ({ ...prev, horario: e.target.value }))}
              required
            />
          </div>
          
          <div className="space-y-2">
            <Label htmlFor="responsavel" className="flex items-center space-x-1">
              <User className="h-4 w-4" />
              <span>Responsável</span>
              <span className="text-red-500">*</span>
            </Label>
            <Select
              value={formData.responsavel_id}
              onValueChange={(value) => setFormData(prev => ({ ...prev, responsavel_id: value }))}
              required
            >
              <SelectTrigger>
                <SelectValue placeholder="Selecione o responsável" />
              </SelectTrigger>
              <SelectContent>
                {users.map(user => (
                  <SelectItem key={user.id} value={user.id}>
                    {user.nome} {user.sobrenome}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          
          <div className="flex justify-end space-x-2 pt-4">
            <Button type="button" variant="outline" onClick={onClose} disabled={isSubmitting}>
              Cancelar
            </Button>
            <Button type="submit" disabled={isSubmitting}>
              {isSubmitting ? 'Criando...' : 'Criar Evento'}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  )
}
