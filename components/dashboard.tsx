"use client"

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { useAuth } from '@/contexts/auth-context'
import { useData } from '@/contexts/data-context'
import { Calendar, CheckCircle, Clock, XCircle, RotateCcw, TrendingUp, Users, FileText } from 'lucide-react'

export default function Dashboard() {
  const { users } = useAuth()
  const { events, getEventStats } = useData()
  const stats = getEventStats()

  const getEventsByUser = () => {
    return users.map(user => ({
      user,
      events: events.filter(e => e.responsavel_id === user.id),
      feitos: events.filter(e => e.responsavel_id === user.id && e.status === 'feito').length
    }))
  }

  const getRecentEvents = () => {
    return events
      .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
      .slice(0, 5)
  }

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'agendado': return 'text-blue-600 bg-blue-100'
      case 'feito': return 'text-green-600 bg-green-100'
      case 'reagendado': return 'text-yellow-600 bg-yellow-100'
      case 'cancelado': return 'text-red-600 bg-red-100'
      default: return 'text-gray-600 bg-gray-100'
    }
  }

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'agendado': return <Clock className="h-4 w-4" />
      case 'feito': return <CheckCircle className="h-4 w-4" />
      case 'reagendado': return <RotateCcw className="h-4 w-4" />
      case 'cancelado': return <XCircle className="h-4 w-4" />
      default: return <Calendar className="h-4 w-4" />
    }
  }

  return (
    <div className="space-y-6">
      {/* Cards de Estatísticas */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total de Eventos</CardTitle>
            <Calendar className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.total}</div>
            <p className="text-xs text-muted-foreground">
              {stats.thisMonth} este mês
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Eventos Concluídos</CardTitle>
            <CheckCircle className="h-4 w-4 text-green-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-green-600">{stats.feitos}</div>
            <p className="text-xs text-muted-foreground">
              {stats.total > 0 ? Math.round((stats.feitos / stats.total) * 100) : 0}% do total
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Agendados</CardTitle>
            <Clock className="h-4 w-4 text-blue-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-blue-600">{stats.agendados}</div>
            <p className="text-xs text-muted-foreground">
              {stats.thisWeek} esta semana
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Reagendados/Cancelados</CardTitle>
            <TrendingUp className="h-4 w-4 text-yellow-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-yellow-600">
              {stats.reagendados + stats.cancelados}
            </div>
            <p className="text-xs text-muted-foreground">
              {stats.reagendados} reagendados, {stats.cancelados} cancelados
            </p>
          </CardContent>
        </Card>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Performance por Usuário */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center space-x-2">
              <Users className="h-5 w-5" />
              <span>Performance por Usuário</span>
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {getEventsByUser().map(({ user, events, feitos }) => (
                <div key={user.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div>
                    <div className="font-medium">{user.nome} {user.sobrenome}</div>
                    <div className="text-sm text-gray-600">
                      {events.length} evento(s) total
                    </div>
                  </div>
                  <div className="text-right">
                    <div className="text-lg font-bold text-green-600">{feitos}</div>
                    <div className="text-xs text-gray-500">concluídos</div>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Eventos Recentes */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center space-x-2">
              <FileText className="h-5 w-5" />
              <span>Eventos Recentes</span>
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {getRecentEvents().map(event => {
                const user = users.find(u => u.id === event.responsavel_id)
                return (
                  <div key={event.id} className="flex items-center justify-between p-3 border rounded-lg">
                    <div className="flex-1">
                      <div className="font-medium">{event.title}</div>
                      <div className="text-sm text-gray-600">
                        {user?.nome} {user?.sobrenome} • {new Date(event.start).toLocaleDateString('pt-BR')}
                      </div>
                    </div>
                    <div className={`flex items-center space-x-1 px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(event.status)}`}>
                      {getStatusIcon(event.status)}
                      <span className="capitalize">{event.status}</span>
                    </div>
                  </div>
                )
              })}
              {events.length === 0 && (
                <div className="text-center text-gray-500 py-4">
                  Nenhum evento criado ainda
                </div>
              )}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
