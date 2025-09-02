"use client"

import { createContext, useContext, useState, useEffect, ReactNode } from 'react'
import { User } from '@/types'

interface AuthContextType {
  user: User | null
  login: (email: string, senha: string) => boolean
  logout: () => void
  users: User[]
  addUser: (user: Omit<User, 'id'>) => void
}

const AuthContext = createContext<AuthContextType | undefined>(undefined)

// Função simples para hash de senha (em produção, use bcrypt)
const hashPassword = (password: string): string => {
  return btoa(password) // Base64 encoding (apenas para demonstração)
}

const verifyPassword = (password: string, hash: string): boolean => {
  return btoa(password) === hash
}

// Dados iniciais de usuários com senhas hasheadas
const initialUsers: User[] = [
  {
    id: '1',
    nome: 'João',
    sobrenome: 'Silva',
    email: 'joao.silva',
    senha: hashPassword('123456')
  },
  {
    id: '2',
    nome: 'Maria',
    sobrenome: 'Santos',
    email: 'maria.santos',
    senha: hashPassword('123456')
  },
  {
    id: '3',
    nome: 'Admin',
    sobrenome: 'Sistema',
    email: 'admin.sistema',
    senha: hashPassword('admin123')
  }
]

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null)
  const [users, setUsers] = useState<User[]>([])

  useEffect(() => {
    // Carregar usuários do localStorage ou usar dados iniciais
    const savedUsers = localStorage.getItem('users')
    if (savedUsers) {
      setUsers(JSON.parse(savedUsers))
    } else {
      setUsers(initialUsers)
      localStorage.setItem('users', JSON.stringify(initialUsers))
    }

    // Verificar se há usuário logado
    const savedUser = localStorage.getItem('user')
    if (savedUser) {
      setUser(JSON.parse(savedUser))
    }
  }, [])

  useEffect(() => {
    // Salvar usuários no localStorage sempre que a lista mudar
    if (users.length > 0) {
      localStorage.setItem('users', JSON.stringify(users))
    }
  }, [users])

  const login = (email: string, senha: string): boolean => {
    const foundUser = users.find(u => u.email === email && verifyPassword(senha, u.senha))
    if (foundUser) {
      setUser(foundUser)
      localStorage.setItem('user', JSON.stringify(foundUser))
      return true
    }
    return false
  }

  const logout = () => {
    setUser(null)
    localStorage.removeItem('user')
  }

  const addUser = (userData: Omit<User, 'id'>) => {
    const newUser: User = {
      ...userData,
      id: Date.now().toString(),
      senha: hashPassword(userData.senha) // Hash da senha
    }
    setUsers(prev => [...prev, newUser])
  }

  return (
    <AuthContext.Provider value={{ user, login, logout, users, addUser }}>
      {children}
    </AuthContext.Provider>
  )
}

export const useAuth = () => {
  const context = useContext(AuthContext)
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider')
  }
  return context
}
