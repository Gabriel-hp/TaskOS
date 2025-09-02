"use client"

import { useState } from 'react'
import { Button } from '@/components/ui/button'
import { useAuth } from '@/contexts/auth-context'
import CalendarView from './calendar-view'
import Dashboard from './dashboard'
import UserManagement from './user-management'
import { Calendar, BarChart3, Users, LogOut } from 'lucide-react'

export default function MainLayout() {
  const { user, logout } = useAuth()
  const [activeTab, setActiveTab] = useState<'calendar' | 'dashboard' | 'users'>('calendar')

  return (
    <div className="min-h-screen bg-gray-50">
      <header className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center py-4">
            <div className="flex items-center space-x-6">
              <div className="flex items-center space-x-3">
                <Calendar className="h-8 w-8 text-blue-600" />
                <h1 className="text-2xl font-bold text-gray-900">
                  Sistema de Agenda
                </h1>
              </div>
              
              <nav className="flex space-x-4">
                <Button
                  variant={activeTab === 'calendar' ? 'default' : 'ghost'}
                  onClick={() => setActiveTab('calendar')}
                  className="flex items-center space-x-2"
                >
                  <Calendar className="h-4 w-4" />
                  <span>Agenda</span>
                </Button>
                <Button
                  variant={activeTab === 'dashboard' ? 'default' : 'ghost'}
                  onClick={() => setActiveTab('dashboard')}
                  className="flex items-center space-x-2"
                >
                  <BarChart3 className="h-4 w-4" />
                  <span>Dashboard</span>
                </Button>
                <Button
                  variant={activeTab === 'users' ? 'default' : 'ghost'}
                  onClick={() => setActiveTab('users')}
                  className="flex items-center space-x-2"
                >
                  <Users className="h-4 w-4" />
                  <span>Usuários</span>
                </Button>
              </nav>
            </div>
            
            <div className="flex items-center space-x-4">
              <span className="text-gray-600">Bem-vindo, {user?.nome}!</span>
              <Button onClick={logout} variant="outline" className="flex items-center space-x-2">
                <LogOut className="h-4 w-4" />
                <span>Sair</span>
              </Button>
            </div>
          </div>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {activeTab === 'calendar' && <CalendarView />}
        {activeTab === 'dashboard' && <Dashboard />}
        {activeTab === 'users' && <UserManagement />}
      </main>
    </div>
  )
}
