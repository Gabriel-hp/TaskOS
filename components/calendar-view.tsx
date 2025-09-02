"use client"

import { useState } from 'react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { useAuth } from '@/contexts/auth-context'
import { useData } from '@/contexts/data-context'
import EventModal from './event-modal'
import OrdemServicoModal from './ordem-servico-modal'
import { ChevronLeft, ChevronRight, Plus, Calendar, Clock, CheckCircle, XCircle, RotateCcw } from 'lucide-react'

export default function CalendarView() {
  const { user, logout, users } = useAuth()
  const { events } = useData()
  const [currentDate, setCurrentDate] = useState(new Date())
  const [selectedDate, setSelectedDate] = useState<string | null>(null)
  const [selectedEvent, setSelectedEvent] = useState<string | null>(null)
  const [showEventModal, setShowEventModal] = useState(false)
  const [showOSModal, setShowOSModal] = useState(false)

  const today = new Date()
  const year = currentDate.getFullYear()
  const month = currentDate.getMonth()

  const firstDayOfMonth = new Date(year, month, 1)
  const lastDayOfMonth = new Date(year, month + 1, 0)
  const firstDayOfWeek = firstDayOfMonth.getDay()
  const daysInMonth = lastDayOfMonth.getDate()

  const monthNames = [
    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
  ]

  const weekDays = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado']

  const previousMonth = () => {
    setCurrentDate(new Date(year, month - 1, 1))
  }

  const nextMonth = () => {
    setCurrentDate(new Date(year, month + 1, 1))
  }

  const goToToday = () => {
    setCurrentDate(new Date())
  }

  const handleDateClick = (day: number) => {
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`
    setSelectedDate(dateStr)
    setShowEventModal(true)
  }

  const getEventsForDate = (day: number) => {
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`
    return events.filter(event => event.start.startsWith(dateStr))
  }

  const formatTime = (dateTimeString: string) => {
    const date = new Date(dateTimeString)
    return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
  }

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'agendado': return 'bg-blue-100 text-blue-800 border-blue-200'
      case 'feito': return 'bg-green-100 text-green-800 border-green-200'
      case 'reagendado': return 'bg-yellow-100 text-yellow-800 border-yellow-200'
      case 'cancelado': return 'bg-red-100 text-red-800 border-red-200'
      default: return 'bg-gray-100 text-gray-800 border-gray-200'
    }
  }

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'feito': return <CheckCircle className="h-3 w-3" />
      case 'reagendado': return <RotateCcw className="h-3 w-3" />
      case 'cancelado': return <XCircle className="h-3 w-3" />
      default: return <Clock className="h-3 w-3" />
    }
  }

  const renderCalendarDays = () => {
    const days = []
    
    // Dias vazios do mês anterior
    for (let i = 0; i < firstDayOfWeek; i++) {
      days.push(
        <div key={`empty-${i}`} className="h-32 border border-gray-200 bg-gray-50"></div>
      )
    }

    // Dias do mês atual
    for (let day = 1; day <= daysInMonth; day++) {
      const dayEvents = getEventsForDate(day)
      const isToday = today.getDate() === day && today.getMonth() === month && today.getFullYear() === year

      days.push(
        <div
          key={day}
          className={`h-32 border border-gray-200 p-2 cursor-pointer hover:bg-gray-50 transition-colors ${
            isToday ? 'bg-blue-50 border-blue-300' : 'bg-white'
          }`}
          onClick={() => handleDateClick(day)}
        >
          <div className={`text-sm font-semibold mb-1 ${isToday ? 'text-blue-600' : 'text-gray-900'}`}>
            {day}
            {isToday && <span className="ml-1 text-xs">(Hoje)</span>}
          </div>
          <div className="space-y-1 overflow-hidden">
            {dayEvents.slice(0, 3).map((event) => (
              <div
                key={event.id}
                className={`text-xs px-2 py-1 rounded cursor-pointer hover:opacity-80 transition-opacity border ${getStatusColor(event.status)}`}
                onClick={(e) => {
                  e.stopPropagation()
                  setSelectedEvent(event.id)
                  setShowOSModal(true)
                }}
                title={`${event.title} - ${formatTime(event.start)} - ${event.status}`}
              >
                <div className="flex items-center space-x-1">
                  {getStatusIcon(event.status)}
                  <span className="font-medium truncate">{event.title}</span>
                </div>
                <div className="flex items-center text-current opacity-75">
                  <Clock className="h-3 w-3 mr-1" />
                  {formatTime(event.start)}
                </div>
              </div>
            ))}
            {dayEvents.length > 3 && (
              <div className="text-xs text-gray-500 font-medium">
                +{dayEvents.length - 3} evento(s)
              </div>
            )}
          </div>
        </div>
      )
    }

    return days
  }

  // Corrigir os próximos eventos
  const getUpcomingEvents = () => {
    const now = new Date()
    return events
      .filter(event => {
        const eventDate = new Date(event.start)
        return eventDate >= now && event.status !== 'cancelado'
      })
      .sort((a, b) => new Date(a.start).getTime() - new Date(b.start).getTime())
      .slice(0, 5)
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <header className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center py-4">
            <div className="flex items-center space-x-3">
              <Calendar className="h-8 w-8 text-blue-600" />
              <h1 className="text-2xl font-bold text-gray-900">
                Sistema de Agenda
              </h1>
            </div>
            <div className="flex items-center space-x-4">
              <span className="text-gray-600">Bem-vindo, {user?.nome}!</span>
              <Button onClick={logout} variant="outline">
                Sair
              </Button>
            </div>
          </div>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <Card>
          <CardHeader>
            <div className="flex items-center justify-between">
              <CardTitle className="text-3xl font-bold text-gray-900">
                {monthNames[month]} {year}
              </CardTitle>
              <div className="flex items-center space-x-2">
                <Button variant="outline" size="sm" onClick={previousMonth}>
                  <ChevronLeft className="h-4 w-4" />
                  Anterior
                </Button>
                <Button variant="outline" size="sm" onClick={goToToday}>
                  Hoje
                </Button>
                <Button variant="outline" size="sm" onClick={nextMonth}>
                  Próximo
                  <ChevronRight className="h-4 w-4" />
                </Button>
                <Button 
                  size="sm" 
                  onClick={() => {
                    const today = new Date()
                    const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`
                    setSelectedDate(todayStr)
                    setShowEventModal(true)
                  }}
                  className="bg-blue-600 hover:bg-blue-700"
                >
                  <Plus className="h-4 w-4 mr-1" />
                  Novo Evento
                </Button>
              </div>
            </div>
            <div className="text-sm text-gray-600 mt-2">
              Total de eventos este mês: {events.filter(event => {
                const eventDate = new Date(event.start)
                return eventDate.getMonth() === month && eventDate.getFullYear() === year
              }).length}
            </div>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-7 gap-0 border border-gray-200 rounded-lg overflow-hidden">
              {weekDays.map(day => (
                <div key={day} className="h-12 flex items-center justify-center font-semibold text-gray-700 border-b border-gray-200 bg-gray-100">
                  <span className="hidden sm:inline">{day}</span>
                  <span className="sm:hidden">{day.slice(0, 3)}</span>
                </div>
              ))}
              {renderCalendarDays()}
            </div>
          </CardContent>
        </Card>

        {/* Próximos eventos corrigido */}
        <Card className="mt-6">
          <CardHeader>
            <CardTitle className="text-lg">Próximos Eventos</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {getUpcomingEvents().map(event => {
                const eventDate = new Date(event.start)
                const responsavel = users.find(u => u.id === event.responsavel_id)
                return (
                  <div 
                    key={event.id}
                    className="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors"
                    onClick={() => {
                      setSelectedEvent(event.id)
                      setShowOSModal(true)
                    }}
                  >
                    <div className="flex-1">
                      <div className="flex items-center space-x-2">
                        <span className="font-medium text-gray-900">{event.title}</span>
                        <div className={`flex items-center space-x-1 px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(event.status)}`}>
                          {getStatusIcon(event.status)}
                          <span className="capitalize">{event.status}</span>
                        </div>
                      </div>
                      <div className="text-sm text-gray-600">{event.endereco}</div>
                      <div className="text-sm text-gray-500">
                        Responsável: {responsavel?.nome} {responsavel?.sobrenome}
                      </div>
                    </div>
                    <div className="text-right">
                      <div className="text-sm font-medium text-gray-900">
                        {eventDate.toLocaleDateString('pt-BR')}
                      </div>
                      <div className="text-sm text-gray-600">
                        {formatTime(event.start)}
                      </div>
                    </div>
                  </div>
                )
              })}
              {getUpcomingEvents().length === 0 && (
                <div className="text-center text-gray-500 py-4">
                  Nenhum evento próximo agendado
                </div>
              )}
            </div>
          </CardContent>
        </Card>
      </main>

      <EventModal
        isOpen={showEventModal}
        onClose={() => setShowEventModal(false)}
        selectedDate={selectedDate}
      />

      <OrdemServicoModal
        isOpen={showOSModal}
        onClose={() => setShowOSModal(false)}
        eventId={selectedEvent}
      />
    </div>
  )
}
