"use client"

import { useState } from 'react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { useAuth } from '@/contexts/auth-context'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { UserPlus, Eye, EyeOff, Check, X } from 'lucide-react'

interface UserRegistrationProps {
  isOpen: boolean
  onClose: () => void
}

export default function UserRegistration({ isOpen, onClose }: UserRegistrationProps) {
  const { addUser, users } = useAuth()
  const [formData, setFormData] = useState({
    nome: '',
    sobrenome: '',
    email: '',
    senha: '',
    confirmarSenha: ''
  })
  const [showPassword, setShowPassword] = useState(false)
  const [showConfirmPassword, setShowConfirmPassword] = useState(false)
  const [errors, setErrors] = useState<string[]>([])
  const [success, setSuccess] = useState(false)
  const [isSubmitting, setIsSubmitting] = useState(false)

  const validateForm = () => {
    const newErrors: string[] = []

    if (!formData.nome.trim()) {
      newErrors.push('Nome é obrigatório')
    }

    if (!formData.sobrenome.trim()) {
      newErrors.push('Sobrenome é obrigatório')
    }

    if (!formData.email.trim()) {
      newErrors.push('Email é obrigatório')
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.push('Email deve ter um formato válido')
    }

    // Verificar se o email já existe
    const emailExists = users.some(user => user.email.toLowerCase() === formData.email.toLowerCase())
    if (emailExists) {
      newErrors.push('Este email já está cadastrado')
    }

    // Verificar se o formato de usuário já existe
    const userFormat = `${formData.nome.toLowerCase()}.${formData.sobrenome.toLowerCase()}`
    const userFormatExists = users.some(user => 
      `${user.nome.toLowerCase()}.${user.sobrenome.toLowerCase()}` === userFormat
    )
    if (userFormatExists) {
      newErrors.push('Já existe um usuário com este nome e sobrenome')
    }

    if (!formData.senha) {
      newErrors.push('Senha é obrigatória')
    } else if (formData.senha.length < 6) {
      newErrors.push('Senha deve ter pelo menos 6 caracteres')
    }

    if (formData.senha !== formData.confirmarSenha) {
      newErrors.push('Senhas não coincidem')
    }

    return newErrors
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setErrors([])
    setSuccess(false)
    setIsSubmitting(true)

    const validationErrors = validateForm()
    if (validationErrors.length > 0) {
      setErrors(validationErrors)
      setIsSubmitting(false)
      return
    }

    try {
      // Simular delay de cadastro
      await new Promise(resolve => setTimeout(resolve, 1000))

      addUser({
        nome: formData.nome.trim(),
        sobrenome: formData.sobrenome.trim(),
        email: formData.email.toLowerCase().trim(),
        senha: formData.senha
      })

      setSuccess(true)
      setFormData({
        nome: '',
        sobrenome: '',
        email: '',
        senha: '',
        confirmarSenha: ''
      })

      // Fechar modal após 2 segundos
      setTimeout(() => {
        setSuccess(false)
        onClose()
      }, 2000)

    } catch (error) {
      setErrors(['Erro ao cadastrar usuário. Tente novamente.'])
    } finally {
      setIsSubmitting(false)
    }
  }

  const getUserFormat = () => {
    if (formData.nome && formData.sobrenome) {
      return `${formData.nome.toLowerCase()}.${formData.sobrenome.toLowerCase()}`
    }
    return ''
  }

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="sm:max-w-[500px]">
        <DialogHeader>
          <DialogTitle className="flex items-center space-x-2">
            <UserPlus className="h-5 w-5 text-blue-600" />
            <span>Cadastrar Novo Usuário</span>
          </DialogTitle>
        </DialogHeader>

        {success ? (
          <div className="text-center py-8">
            <div className="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
              <Check className="h-8 w-8 text-green-600" />
            </div>
            <h3 className="text-lg font-semibold text-gray-900 mb-2">
              Usuário cadastrado com sucesso!
            </h3>
            <p className="text-gray-600">
              O usuário pode fazer login com: <strong>{getUserFormat()}</strong>
            </p>
          </div>
        ) : (
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="nome" className="flex items-center space-x-1">
                  <span>Nome</span>
                  <span className="text-red-500">*</span>
                </Label>
                <Input
                  id="nome"
                  value={formData.nome}
                  onChange={(e) => setFormData(prev => ({ ...prev, nome: e.target.value }))}
                  placeholder="Digite o nome"
                  disabled={isSubmitting}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="sobrenome" className="flex items-center space-x-1">
                  <span>Sobrenome</span>
                  <span className="text-red-500">*</span>
                </Label>
                <Input
                  id="sobrenome"
                  value={formData.sobrenome}
                  onChange={(e) => setFormData(prev => ({ ...prev, sobrenome: e.target.value }))}
                  placeholder="Digite o sobrenome"
                  disabled={isSubmitting}
                />
              </div>
            </div>

            {getUserFormat() && (
              <div className="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p className="text-sm text-blue-800">
                  <strong>Usuário para login:</strong> {getUserFormat()}
                </p>
              </div>
            )}

            <div className="space-y-2">
              <Label htmlFor="email" className="flex items-center space-x-1">
                <span>Email</span>
                <span className="text-red-500">*</span>
              </Label>
              <Input
                id="email"
                type="email"
                value={formData.email}
                onChange={(e) => setFormData(prev => ({ ...prev, email: e.target.value }))}
                placeholder="Digite o email"
                disabled={isSubmitting}
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="senha" className="flex items-center space-x-1">
                <span>Senha</span>
                <span className="text-red-500">*</span>
              </Label>
              <div className="relative">
                <Input
                  id="senha"
                  type={showPassword ? 'text' : 'password'}
                  value={formData.senha}
                  onChange={(e) => setFormData(prev => ({ ...prev, senha: e.target.value }))}
                  placeholder="Digite a senha (mín. 6 caracteres)"
                  disabled={isSubmitting}
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                  onClick={() => setShowPassword(!showPassword)}
                  disabled={isSubmitting}
                >
                  {showPassword ? (
                    <EyeOff className="h-4 w-4 text-gray-400" />
                  ) : (
                    <Eye className="h-4 w-4 text-gray-400" />
                  )}
                </Button>
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="confirmarSenha" className="flex items-center space-x-1">
                <span>Confirmar Senha</span>
                <span className="text-red-500">*</span>
              </Label>
              <div className="relative">
                <Input
                  id="confirmarSenha"
                  type={showConfirmPassword ? 'text' : 'password'}
                  value={formData.confirmarSenha}
                  onChange={(e) => setFormData(prev => ({ ...prev, confirmarSenha: e.target.value }))}
                  placeholder="Confirme a senha"
                  disabled={isSubmitting}
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                  onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                  disabled={isSubmitting}
                >
                  {showConfirmPassword ? (
                    <EyeOff className="h-4 w-4 text-gray-400" />
                  ) : (
                    <Eye className="h-4 w-4 text-gray-400" />
                  )}
                </Button>
              </div>
            </div>

            {errors.length > 0 && (
              <Alert variant="destructive">
                <X className="h-4 w-4" />
                <AlertDescription>
                  <ul className="list-disc list-inside space-y-1">
                    {errors.map((error, index) => (
                      <li key={index}>{error}</li>
                    ))}
                  </ul>
                </AlertDescription>
              </Alert>
            )}

            <div className="flex justify-end space-x-2 pt-4">
              <Button 
                type="button" 
                variant="outline" 
                onClick={onClose}
                disabled={isSubmitting}
              >
                Cancelar
              </Button>
              <Button 
                type="submit" 
                disabled={isSubmitting}
                className="bg-blue-600 hover:bg-blue-700"
              >
                {isSubmitting ? 'Cadastrando...' : 'Cadastrar Usuário'}
              </Button>
            </div>
          </form>
        )}
      </DialogContent>
    </Dialog>
  )
}
