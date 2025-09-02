"use client"

import { useState } from 'react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { useAuth } from '@/contexts/auth-context'
import { useData } from '@/contexts/data-context'
import UserRegistration from './user-registration'
import { UserPlus, Users, Mail, Calendar, CheckCircle } from 'lucide-react'

export default function UserManagement() {
  const { users } = useAuth()
  const { events } = useData()
  const [showRegistration, setShowRegistration] = useState(false)

  const getUserStats = (userId: string) => {
    const userEvents = events.filter(e => e.responsavel_id === userId)
    const completedEvents = userEvents.filter(e => e.status === 'feito')
    
    return {
      totalEvents: userEvents.length,
      completedEvents: completedEvents.length,
      completionRate: userEvents.length > 0 ? Math.round((completedEvents.length / userEvents.length) * 100) : 0
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Gerenciamento de Usuários</h2>
          <p className="text-gray-600">Gerencie os usuários do sistema</p>
        </div>
        <Button 
          onClick={() => setShowRegistration(true)}
          className="bg-blue-600 hover:bg-blue-700 flex items-center space-x-2"
        >
          <UserPlus className="h-4 w-4" />
          <span>Novo Usuário</span>
        </Button>
      </div>

      {/* Estatísticas Gerais */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total de Usuários</CardTitle>
            <Users className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{users.length}</div>
            <p className="text-xs text-muted-foreground">
              Usuários cadastrados no sistema
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Usuários Ativos</CardTitle>
            <CheckCircle className="h-4 w-4 text-green-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-green-600">
              {users.filter(user => {
                const userEvents = events.filter(e => e.responsavel_id === user.id)
                return userEvents.length > 0
              }).length}
            </div>
            <p className="text-xs text-muted-foreground">
              Com eventos cadastrados
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Taxa de Conclusão Média</CardTitle>
            <Calendar className="h-4 w-4 text-blue-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-blue-600">
              {users.length > 0 ? Math.round(
                users.reduce((acc, user) => {
                  const stats = getUserStats(user.id)
                  return acc + stats.completionRate
                }, 0) / users.length
              ) : 0}%
            </div>
            <p className="text-xs text-muted-foreground">
              Média de eventos concluídos
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Lista de Usuários */}
      <Card>
        <CardHeader>
          <CardTitle>Usuários Cadastrados</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {users.map(user => {
              const stats = getUserStats(user.id)
              const userFormat = `${user.nome.toLowerCase()}.${user.sobrenome.toLowerCase()}`
              
              return (
                <div key={user.id} className="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                  <div className="flex items-center space-x-4">
                    <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                      <span className="text-blue-600 font-semibold text-lg">
                        {user.nome.charAt(0)}{user.sobrenome.charAt(0)}
                      </span>
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900">
                        {user.nome} {user.sobrenome}
                      </h3>
                      <div className="flex items-center space-x-4 text-sm text-gray-600">
                        <div className="flex items-center space-x-1">
                          <Users className="h-4 w-4" />
                          <span>{userFormat}</span>
                        </div>
                        <div className="flex items-center space-x-1">
                          <Mail className="h-4 w-4" />
                          <span>{user.email}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div className="text-right">
                    <div className="flex items-center space-x-6">
                      <div className="text-center">
                        <div className="text-lg font-bold text-gray-900">{stats.totalEvents}</div>
                        <div className="text-xs text-gray-500">Eventos</div>
                      </div>
                      <div className="text-center">
                        <div className="text-lg font-bold text-green-600">{stats.completedEvents}</div>
                        <div className="text-xs text-gray-500">Concluídos</div>
                      </div>
                      <div className="text-center">
                        <div className={`text-lg font-bold ${
                          stats.completionRate >= 80 ? 'text-green-600' :
                          stats.completionRate >= 60 ? 'text-yellow-600' :
                          'text-red-600'
                        }`}>
                          {stats.completionRate}%
                        </div>
                        <div className="text-xs text-gray-500">Taxa</div>
                      </div>
                    </div>
                  </div>
                </div>
              )
            })}
            
            {users.length === 0 && (
              <div className="text-center py-8 text-gray-500">
                <Users className="h-12 w-12 mx-auto mb-4 text-gray-300" />
                <p>Nenhum usuário cadastrado ainda</p>
                <Button 
                  onClick={() => setShowRegistration(true)}
                  className="mt-4"
                  variant="outline"
                >
                  Cadastrar Primeiro Usuário
                </Button>
              </div>
            )}
          </div>
        </CardContent>
      </Card>

      <UserRegistration 
        isOpen={showRegistration}
        onClose={() => setShowRegistration(false)}
      />
    </div>
  )
}
