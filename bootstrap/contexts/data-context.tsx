"use client"

import { createContext, useContext, useState, ReactNode } from 'react'
import { Event, OrdemServico } from '@/types'

interface DataContextType {
  events: Event[]
  ordensServico: OrdemServico[]
  addEvent: (event: Omit<Event, 'id' | 'status' | 'created_at'>) => string
  updateEventStatus: (eventId: string, status: Event['status']) => void
  addOrdemServico: (os: Omit<OrdemServico, 'id'>) => void
  getOrdemServicoByEventId: (eventId: string) => OrdemServico | undefined
  updateOrdemServico: (id: string, data: Partial<OrdemServico>) => void
  getEventStats: () => {
    total: number
    agendados: number
    feitos: number
    reagendados: number
    cancelados: number
    thisMonth: number
    thisWeek: number
  }
}

const DataContext = createContext<DataContextType | undefined>(undefined)

export function DataProvider({ children }: { children: ReactNode }) {
  const [events, setEvents] = useState<Event[]>([])
  const [ordensServico, setOrdensServico] = useState<OrdemServico[]>([])

  const addEvent = (eventData: Omit<Event, 'id' | 'status' | 'created_at'>): string => {
    const newEvent: Event = {
      ...eventData,
      id: Date.now().toString(),
      status: 'agendado',
      created_at: new Date().toISOString()
    }
    setEvents(prev => [...prev, newEvent])

    // Criar automaticamente uma O.S. para o evento
    const newOS: OrdemServico = {
      id: (Date.now() + 1).toString(),
      evento_id: newEvent.id,
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
    }
    setOrdensServico(prev => [...prev, newOS])

    return newEvent.id
  }

  const updateEventStatus = (eventId: string, status: Event['status']) => {
    setEvents(prev => 
      prev.map(event => 
        event.id === eventId ? { ...event, status } : event
      )
    )
  }

  const addOrdemServico = (osData: Omit<OrdemServico, 'id'>) => {
    const newOS: OrdemServico = {
      ...osData,
      id: Date.now().toString()
    }
    setOrdensServico(prev => [...prev, newOS])
  }

  const getOrdemServicoByEventId = (eventId: string): OrdemServico | undefined => {
    return ordensServico.find(os => os.evento_id === eventId)
  }

  const updateOrdemServico = (id: string, data: Partial<OrdemServico>) => {
    setOrdensServico(prev => 
      prev.map(os => os.id === id ? { ...os, ...data } : os)
    )
  }

  const getEventStats = () => {
    const now = new Date()
    const thisMonth = now.getMonth()
    const thisYear = now.getFullYear()
    const startOfWeek = new Date(now)
    startOfWeek.setDate(now.getDate() - now.getDay())
    const endOfWeek = new Date(startOfWeek)
    endOfWeek.setDate(startOfWeek.getDate() + 6)

    return {
      total: events.length,
      agendados: events.filter(e => e.status === 'agendado').length,
      feitos: events.filter(e => e.status === 'feito').length,
      reagendados: events.filter(e => e.status === 'reagendado').length,
      cancelados: events.filter(e => e.status === 'cancelado').length,
      thisMonth: events.filter(e => {
        const eventDate = new Date(e.start)
        return eventDate.getMonth() === thisMonth && eventDate.getFullYear() === thisYear
      }).length,
      thisWeek: events.filter(e => {
        const eventDate = new Date(e.start)
        return eventDate >= startOfWeek && eventDate <= endOfWeek
      }).length
    }
  }

  return (
    <DataContext.Provider value={{
      events,
      ordensServico,
      addEvent,
      updateEventStatus,
      addOrdemServico,
      getOrdemServicoByEventId,
      updateOrdemServico,
      getEventStats
    }}>
      {children}
    </DataContext.Provider>
  )
}

export const useData = () => {
  const context = useContext(DataContext)
  if (context === undefined) {
    throw new Error('useData must be used within a DataProvider')
  }
  return context
}
