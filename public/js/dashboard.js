// Variáveis globais
const currentDate = new Date()
let eventos = []
let ordensServico = []
let usuarios = []
let currentEventId = null
let currentOSId = null
const bootstrap = window.bootstrap // Declare the bootstrap variable

// Inicialização
document.addEventListener("DOMContentLoaded", () => {
  loadInitialData()
  setupEventListeners()
  renderCalendar()
})

// Carregar dados iniciais
async function loadInitialData() {
  try {
    const [eventosResponse, osResponse, usersResponse] = await Promise.all([
      fetch("/api/eventos"),
      fetch("/api/os"),
      fetch("/api/users"),
    ])

    eventos = await eventosResponse.json()
    ordensServico = await osResponse.json()
    usuarios = await usersResponse.json()

    renderCalendar()
    renderEventsList()
    renderOSList()
    renderUsersList()
  } catch (error) {
    console.error("Erro ao carregar dados:", error)
    showAlert("Erro ao carregar dados", "danger")
  }
}

// Configurar event listeners
function setupEventListeners() {
  // Navegação do calendário
  document.getElementById("prevMonth").addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1)
    renderCalendar()
  })

  document.getElementById("nextMonth").addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1)
    renderCalendar()
  })

  // Formulários
  document.getElementById("addEventForm").addEventListener("submit", handleAddEvent)
  document.getElementById("addUserForm").addEventListener("submit", handleAddUser)
  document.getElementById("createOSForm").addEventListener("submit", handleCreateOS)

  // Filtros
  document.getElementById("searchEvents").addEventListener("input", renderEventsList)
  document.getElementById("filterEventStatus").addEventListener("change", renderEventsList)
  document.getElementById("filterEventResponsavel").addEventListener("change", renderEventsList)

  document.getElementById("searchOS").addEventListener("input", renderOSList)
  document.getElementById("filterOSStatus").addEventListener("change", renderOSList)
  document.getElementById("filterOSResponsavel").addEventListener("change", renderOSList)

  document.getElementById("searchUsers").addEventListener("input", renderUsersList)
}

// Renderizar calendário
function renderCalendar() {
  const monthNames = [
    "Janeiro",
    "Fevereiro",
    "Março",
    "Abril",
    "Maio",
    "Junho",
    "Julho",
    "Agosto",
    "Setembro",
    "Outubro",
    "Novembro",
    "Dezembro",
  ]

  document.getElementById("currentMonth").textContent =
    `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`

  const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1)
  const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0)
  const startDate = new Date(firstDay)
  startDate.setDate(startDate.getDate() - firstDay.getDay())

  const calendarGrid = document.getElementById("calendarGrid")
  calendarGrid.innerHTML = ""

  for (let i = 0; i < 42; i++) {
    const cellDate = new Date(startDate)
    cellDate.setDate(startDate.getDate() + i)

    const dayElement = document.createElement("div")
    dayElement.className = "calendar-day"

    if (cellDate.getMonth() !== currentDate.getMonth()) {
      dayElement.classList.add("other-month")
    }

    if (isToday(cellDate)) {
      dayElement.classList.add("today")
    }

    dayElement.innerHTML = `
            <div class="fw-bold mb-1">${cellDate.getDate()}</div>
            <div class="events-container">
                ${renderDayEvents(cellDate)}
            </div>
        `

    calendarGrid.appendChild(dayElement)
  }
}

// Renderizar eventos do dia
function renderDayEvents(date) {
  const dayEvents = eventos.filter((evento) => {
    const eventoDate = new Date(evento.data_hora)
    return eventoDate.toDateString() === date.toDateString()
  })

  let html = ""
  dayEvents.slice(0, 3).forEach((evento) => {
    const statusClass = getStatusClass(evento.status)
    html += `
            <div class="event-item ${statusClass}" onclick="showEventDetails(${evento.id})" title="${evento.titulo}">
                <div class="d-flex align-items-center mb-1">
                    <i class="fas fa-clock me-1" style="font-size: 0.6rem;"></i>
                    <span>${formatTime(evento.data_hora)}</span>
                </div>
                <div class="fw-bold">${truncateText(evento.titulo, 15)}</div>
                <div class="text-muted">${truncateText(evento.assunto, 20)}</div>
            </div>
        `
  })

  if (dayEvents.length > 3) {
    html += `<div class="text-muted text-center small">+${dayEvents.length - 3} mais</div>`
  }

  return html
}

// Renderizar lista de eventos
function renderEventsList() {
  const search = document.getElementById("searchEvents").value.toLowerCase()
  const statusFilter = document.getElementById("filterEventStatus").value
  const responsavelFilter = document.getElementById("filterEventResponsavel").value

  const filteredEventos = eventos.filter((evento) => {
    const matchesSearch = evento.titulo.toLowerCase().includes(search) || evento.assunto.toLowerCase().includes(search)
    const matchesStatus = !statusFilter || evento.status === statusFilter
    const matchesResponsavel = !responsavelFilter || evento.responsavel_id == responsavelFilter

    return matchesSearch && matchesStatus && matchesResponsavel
  })

  const container = document.getElementById("eventsList")

  if (filteredEventos.length === 0) {
    container.innerHTML = '<div class="text-center text-muted py-4">Nenhum evento encontrado.</div>'
    return
  }

  container.innerHTML = filteredEventos
    .map((evento) => {
      const responsavel = usuarios.find((u) => u.id == evento.responsavel_id)
      const statusBadge = getStatusBadge(evento.status)

      return `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title mb-0">${evento.titulo}</h6>
                        ${statusBadge}
                    </div>
                    <p class="card-text text-muted mb-2">${evento.assunto}</p>
                    <div class="row text-sm">
                        <div class="col-md-6">
                            <i class="fas fa-calendar me-2"></i>${formatDate(evento.data_hora)}
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-clock me-2"></i>${formatTime(evento.data_hora)}
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-map-marker-alt me-2"></i>${truncateText(evento.endereco, 30)}
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-user me-2"></i>${responsavel ? responsavel.nome + " " + responsavel.sobrenome : "N/A"}
                        </div>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="showEventDetails(${evento.id})">
                            Ver Detalhes
                        </button>
                    </div>
                </div>
            </div>
        `
    })
    .join("")
}

// Renderizar lista de O.S.
function renderOSList() {
  const search = document.getElementById("searchOS").value.toLowerCase()
  const statusFilter = document.getElementById("filterOSStatus").value
  const responsavelFilter = document.getElementById("filterOSResponsavel").value

  const filteredOS = ordensServico.filter((os) => {
    const matchesSearch =
      os.protocolo.toLowerCase().includes(search) ||
      os.cliente_nome.toLowerCase().includes(search) ||
      os.designacao.toLowerCase().includes(search)
    const matchesStatus = !statusFilter || os.status === statusFilter
    const matchesResponsavel = !responsavelFilter || os.responsavel_id == responsavelFilter

    return matchesSearch && matchesStatus && matchesResponsavel
  })

  const container = document.getElementById("osList")

  if (filteredOS.length === 0) {
    container.innerHTML = '<div class="text-center text-muted py-4">Nenhuma O.S. encontrada.</div>'
    return
  }

  container.innerHTML = filteredOS
    .map((os) => {
      const responsavel = usuarios.find((u) => u.id == os.responsavel_id)
      const statusBadge = getStatusBadge(os.status)

      return `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title mb-0">${os.protocolo}</h6>
                        ${statusBadge}
                    </div>
                    <p class="card-text text-muted mb-2">${os.designacao}</p>
                    <div class="row text-sm">
                        <div class="col-md-6">
                            <i class="fas fa-user me-2"></i>${os.cliente_nome}
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-calendar me-2"></i>${formatDate(os.data_hora_inicio)}
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-clock me-2"></i>${formatTime(os.data_hora_inicio)}
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-user-tie me-2"></i>${responsavel ? responsavel.nome + " " + responsavel.sobrenome : "N/A"}
                        </div>
                        <div class="col-12">
                            <i class="fas fa-map-marker-alt me-2"></i>${truncateText(os.endereco, 50)}
                        </div>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="showOSDetails(${os.id})">
                            Ver Detalhes
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="printOS(${os.id})">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
            </div>
        `
    })
    .join("")
}

// Renderizar lista de usuários
function renderUsersList() {
  const search = document.getElementById("searchUsers").value.toLowerCase()

  const filteredUsers = usuarios.filter((user) => {
    return (
      user.nome.toLowerCase().includes(search) ||
      user.sobrenome.toLowerCase().includes(search) ||
      user.email.toLowerCase().includes(search)
    )
  })

  const container = document.getElementById("usersList")

  if (filteredUsers.length === 0) {
    container.innerHTML = '<div class="text-center text-muted py-4">Nenhum usuário encontrado.</div>'
    return
  }

  container.innerHTML = filteredUsers
    .map((user) => {
      return `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">${user.nome} ${user.sobrenome}</h6>
                                <p class="mb-1 text-muted">${user.email}</p>
                                <small class="badge bg-light text-dark">${user.username || user.nome.toLowerCase() + "." + user.sobrenome.toLowerCase()}</small>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-warning me-2" onclick="editUser(${user.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `
    })
    .join("")
}

// Manipular adição de evento
async function handleAddEvent(e) {
  e.preventDefault()

  const formData = {
    titulo: document.getElementById("eventTitulo").value,
    assunto: document.getElementById("eventAssunto").value,
    endereco: document.getElementById("eventEndereco").value,
    data_hora: document.getElementById("eventData").value + "T" + document.getElementById("eventHora").value,
    responsavel_id: document.getElementById("eventResponsavel").value,
  }

  try {
    const response = await fetch("/api/eventos", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": window.csrfToken,
      },
      body: JSON.stringify(formData),
    })

    const result = await response.json()

    if (result.success) {
      eventos.push(result.evento)
      renderCalendar()
      renderEventsList()

      // Fechar modal e limpar formulário
      const modal = bootstrap.Modal.getInstance(document.getElementById("addEventModal"))
      modal.hide()
      document.getElementById("addEventForm").reset()

      showAlert("Evento adicionado com sucesso!", "success")
    } else {
      showAlert("Erro ao adicionar evento", "danger")
    }
  } catch (error) {
    console.error("Erro:", error)
    showAlert("Erro ao adicionar evento", "danger")
  }
}

// Manipular adição de usuário
async function handleAddUser(e) {
  e.preventDefault()

  const formData = {
    nome: document.getElementById("userNome").value,
    sobrenome: document.getElementById("userSobrenome").value,
    email: document.getElementById("userEmail").value,
    senha: document.getElementById("userSenha").value,
  }

  try {
    const response = await fetch("/api/users", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": window.csrfToken,
      },
      body: JSON.stringify(formData),
    })

    const result = await response.json()

    if (result.success) {
      usuarios.push(result.user)
      renderUsersList()

      // Fechar modal e limpar formulário
      const modal = bootstrap.Modal.getInstance(document.getElementById("addUserModal"))
      modal.hide()
      document.getElementById("addUserForm").reset()

      showAlert("Usuário adicionado com sucesso!", "success")
    } else {
      showAlert("Erro ao adicionar usuário", "danger")
    }
  } catch (error) {
    console.error("Erro:", error)
    showAlert("Erro ao adicionar usuário", "danger")
  }
}

// Manipular criação de O.S.
async function handleCreateOS(e) {
  e.preventDefault()

  const formData = {
    evento_id: document.getElementById("osEventoId").value,
    protocolo: document.getElementById("osProtocolo").value,
    cliente_nome: document.getElementById("osClienteNome").value,
    endereco: document.getElementById("osEndereco").value,
    designacao: document.getElementById("osDesignacao").value,
    observacao: document.getElementById("osObservacao").value,
  }

  try {
    const response = await fetch("/api/os", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": window.csrfToken,
      },
      body: JSON.stringify(formData),
    })

    const result = await response.json()

    if (result.success) {
      ordensServico.push(result.os)
      renderOSList()

      // Fechar modal e limpar formulário
      const modal = bootstrap.Modal.getInstance(document.getElementById("createOSModal"))
      modal.hide()
      document.getElementById("createOSForm").reset()

      showAlert("O.S. criada com sucesso!", "success")
    } else {
      showAlert("Erro ao criar O.S.", "danger")
    }
  } catch (error) {
    console.error("Erro:", error)
    showAlert("Erro ao criar O.S.", "danger")
  }
}

// Mostrar detalhes do evento
async function showEventDetails(eventoId) {
  try {
    const response = await fetch(`/api/eventos/${eventoId}`)
    const evento = await response.json()

    currentEventId = eventoId
    const responsavel = usuarios.find((u) => u.id == evento.responsavel_id)
    const temOS = evento.ordem_servico !== null

    const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-tag me-2"></i>Título</h6>
                    <p>${evento.titulo}</p>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-user me-2"></i>Responsável</h6>
                    <p>${responsavel ? responsavel.nome + " " + responsavel.sobrenome : "N/A"}</p>
                </div>
            </div>
            
            <div class="mb-3">
                <h6><i class="fas fa-comment me-2"></i>Assunto</h6>
                <p>${evento.assunto}</p>
            </div>
            
            <div class="mb-3">
                <h6><i class="fas fa-map-marker-alt me-2"></i>Endereço</h6>
                <p>${evento.endereco}</p>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-calendar me-2"></i>Data/Hora</h6>
                    <p>${formatDateTime(evento.data_hora)}</p>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-info-circle me-2"></i>Status</h6>
                    <p>${getStatusBadge(evento.status)}</p>
                </div>
            </div>
            
            <div class="mt-4">
                <div class="btn-group me-2" role="group">
                    <button class="btn btn-sm btn-outline-warning" onclick="updateEventStatus(${eventoId}, 'Pendente')" ${evento.status === "Pendente" ? "disabled" : ""}>
                        Pendente
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="updateEventStatus(${eventoId}, 'Em execução')" ${evento.status === "Em execução" ? "disabled" : ""}>
                        Em Execução
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="updateEventStatus(${eventoId}, 'Finalizado')" ${evento.status === "Finalizado" ? "disabled" : ""}>
                        Finalizado
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="updateEventStatus(${eventoId}, 'Reagendado')" ${evento.status === "Reagendado" ? "disabled" : ""}>
                        Reagendado
                    </button>
                </div>
                
                ${
                  !temOS
                    ? `<button class="btn btn-sm btn-primary" onclick="showCreateOS(${eventoId})">
                    <i class="fas fa-clipboard-list me-2"></i>Fazer O.S.
                </button>`
                    : '<span class="badge bg-success">O.S. já criada</span>'
                }
            </div>
        `

    document.getElementById("eventDetailsContent").innerHTML = content
    const modal = new bootstrap.Modal(document.getElementById("eventDetailsModal"))
    modal.show()
  } catch (error) {
    console.error("Erro:", error)
    showAlert("Erro ao carregar detalhes do evento", "danger")
  }
}

// Mostrar formulário de criar O.S.
function showCreateOS(eventoId) {
  const evento = eventos.find((e) => e.id == eventoId)
  if (!evento) return

  document.getElementById("osEventoId").value = eventoId
  document.getElementById("osEndereco").value = evento.endereco
  document.getElementById("osMotivo").value = evento.assunto

  // Fechar modal de detalhes do evento
  const eventModal = bootstrap.Modal.getInstance(document.getElementById("eventDetailsModal"))
  eventModal.hide()

  // Abrir modal de criar O.S.
  const osModal = new bootstrap.Modal(document.getElementById("createOSModal"))
  osModal.show()
}

// Mostrar detalhes da O.S.
async function showOSDetails(osId) {
  try {
    const response = await fetch(`/api/os/${osId}`)
    const os = await response.json()

    currentOSId = osId
    const responsavel = usuarios.find((u) => u.id == os.responsavel_id)

    const content = `
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Informações da O.S.</h6>
                    ${getStatusBadge(os.status)}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Protocolo</h6>
                            <p>${os.protocolo}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Cliente</h6>
                            <p>${os.cliente_nome}</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Endereço</h6>
                        <p>${os.endereco}</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Designação</h6>
                            <p>${os.designacao}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Responsável</h6>
                            <p>${responsavel ? responsavel.nome + " " + responsavel.sobrenome : "N/A"}</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Motivo</h6>
                        <p>${os.motivo}</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-calendar me-2"></i>Data/Hora</h6>
                            <p>${formatDateTime(os.data_hora_inicio)}</p>
                        </div>
                    </div>
                    
                    ${
                      os.observacao
                        ? `
                        <div class="mb-3">
                            <h6>Observações</h6>
                            <p>${os.observacao}</p>
                        </div>
                    `
                        : ""
                    }
                    
                    ${
                      os.anexo
                        ? `
                        <div class="mb-3">
                            <h6>Anexo</h6>
                            <p><a href="/storage/${os.anexo}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-2"></i>Baixar Anexo
                            </a></p>
                        </div>
                    `
                        : ""
                    }
                </div>
            </div>
            
            <div class="mt-3">
                <div class="btn-group me-2" role="group">
                    <button class="btn btn-sm btn-outline-warning" onclick="updateOSStatus(${osId}, 'Pendente')" ${os.status === "Pendente" ? "disabled" : ""}>
                        Pendente
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="updateOSStatus(${osId}, 'Em execução')" ${os.status === "Em execução" ? "disabled" : ""}>
                        Em Execução
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="updateOSStatus(${osId}, 'Finalizado')" ${os.status === "Finalizado" ? "disabled" : ""}>
                        Finalizado
                    </button>
                </div>
                
                <button class="btn btn-sm btn-outline-primary" onclick="printOS(${osId})">
                    <i class="fas fa-print me-2"></i>Imprimir O.S.
                </button>
            </div>
            
            ${
              os.status !== "Finalizado"
                ? `
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Finalizar O.S.</h6>
                    </div>
                    <div class="card-body">
                        <form id="finalizarOSForm" onsubmit="finalizarOS(event, ${osId})">
                            <div class="mb-3">
                                <label class="form-label">Observações do Técnico</label>
                                <textarea class="form-control" id="osObservacaoFinal" rows="3" placeholder="Digite as observações sobre o serviço realizado..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Upload da O.S. Assinada</label>
                                <input type="file" class="form-control" id="osAnexo" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check me-2"></i>Finalizar O.S.
                            </button>
                        </form>
                    </div>
                </div>
            `
                : ""
            }
        `

    document.getElementById("osDetailsContent").innerHTML = content
    const modal = new bootstrap.Modal(document.getElementById("osDetailsModal"))
    modal.show()
  } catch (error) {
    console.error("Erro:", error)
    showAlert("Erro ao carregar detalhes da O.S.", "danger")
  }
}

// Atualizar status do evento
async function updateEventStatus(eventoId, status) {
  try {
    const response = await fetch(`/api/eventos/${eventoId}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": window.csrfToken,
      },
      body: JSON.stringify({ status }),
    })

    const result = await response.json()

    if (result.success) {
      // Atualizar evento na lista local
      const eventoIndex = eventos.findIndex((e) => e.id == eventoId)
      if (eventoIndex !== -1) {
        eventos[eventoIndex] = result.evento
      }

      renderCalendar()
      renderEventsList()
      showAlert("Status atualizado com sucesso!", "success")

      // Atualizar modal se estiver aberto
      if (currentEventId == eventoId) {
        showEventDetails(eventoId)
      }
    } else {
      showAlert("Erro ao atualizar status", "danger")
    }
  } catch (error) {
    console.error("Erro:", error)
    showAlert("Erro ao atualizar status", "danger")
  }
}

// Atualizar status da O.S.
async function updateOSStatus(osId, status) {
  try {
    const response = await fetch(`/api/os/${osId}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": window.csrfToken,
      },
      body: JSON.stringify({ status }),
    })

    const result = await response.json()

    if (result.success) {
      // Atualizar O.S. na lista local
      const osIndex = ordensServico.findIndex((os) => os.id == osId)
      if (osIndex !== -1) {
        ordensServico[osIndex] = result.os
      }

      renderOSList()
      showAlert("Status atualizado com sucesso!", "success")

      // Atualizar modal se estiver aberto
      if (currentOSId == osId) {
        showOSDetails(osId)
      }
    } else {
      showAlert("Erro ao atualizar status", "danger")
    }
  } catch (error) {
    console.error("Erro:", error)
    showAlert("Erro ao atualizar status", "danger")
  }
}

// Finalizar O.S.
async function finalizarOS(event, osId) {
  event.preventDefault()

  const formData = new FormData()
  formData.append("status", "Finalizado")
  formData.append("observacao", document.getElementById("osObservacaoFinal").value)

  const anexo = document.getElementById("osAnexo").files[0]
  if (anexo) {
    formData.append("anexo", anexo)
  }

  try {
    const response = await fetch(`/api/os/${osId}`, {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": window.csrfToken,
        "X-HTTP-Method-Override": "PUT",
      },
      body: formData,
    })

    const result = await response.json()

    if (result.success) {
      // Atualizar O.S. na lista local
      const osIndex = ordensServico.findIndex((os) => os.id == osId)
      if (osIndex !== -1) {
        ordensServico[osIndex] = result.os
      }

      renderOSList()
      showAlert("O.S. finalizada com sucesso!", "success")

      // Fechar modal
      const modal = bootstrap.Modal.getInstance(document.getElementById("osDetailsModal"))
      modal.hide()
    } else {
      showAlert("Erro ao finalizar O.S.", "danger")
    }
  } catch (error) {
    console.error("Erro:", error)
    showAlert("Erro ao finalizar O.S.", "danger")
  }
}

// Imprimir O.S.
function printOS(osId) {
  window.open(`/os/${osId}/print`, "_blank")
}

// Editar usuário
function editUser(userId) {
  // Implementar edição de usuário
  showAlert("Funcionalidade de edição em desenvolvimento", "info")
}

// Excluir usuário
async function deleteUser(userId) {
  if (!confirm("Tem certeza que deseja excluir este usuário?")) {
    return
  }

  try {
    const response = await fetch(`/api/users/${userId}`, {
      method: "DELETE",
      headers: {
        "X-CSRF-TOKEN": window.csrfToken,
      },
    })

    const result = await response.json()

    if (result.success) {
      usuarios = usuarios.filter((u) => u.id != userId)
      renderUsersList()
      showAlert("Usuário excluído com sucesso!", "success")
    } else {
      showAlert(result.message || "Erro ao excluir usuário", "danger")
    }
  } catch (error) {
    console.error("Erro:", error)
    showAlert("Erro ao excluir usuário", "danger")
  }
}

// Gerar relatório
function generateReport() {
  showAlert("Funcionalidade de relatório em desenvolvimento", "info")
}

// Funções utilitárias
function isToday(date) {
  const today = new Date()
  return date.toDateString() === today.toDateString()
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString("pt-BR")
}

function formatTime(dateString) {
  return new Date(dateString).toLocaleTimeString("pt-BR", { hour: "2-digit", minute: "2-digit" })
}

function formatDateTime(dateString) {
  const date = new Date(dateString)
  return (
    date.toLocaleDateString("pt-BR") + " " + date.toLocaleTimeString("pt-BR", { hour: "2-digit", minute: "2-digit" })
  )
}

function truncateText(text, maxLength) {
  return text.length > maxLength ? text.substring(0, maxLength) + "..." : text
}

function getStatusClass(status) {
  const classes = {
    Pendente: "event-pendente",
    "Em execução": "event-execucao",
    Finalizado: "event-finalizado",
    Reagendado: "event-reagendado",
  }
  return classes[status] || "event-pendente"
}

function getStatusBadge(status) {
  const badges = {
    Pendente: '<span class="badge bg-warning">Pendente</span>',
    "Em execução": '<span class="badge bg-info">Em execução</span>',
    Finalizado: '<span class="badge bg-success">Finalizado</span>',
    Reagendado: '<span class="badge bg-danger">Reagendado</span>',
  }
  return badges[status] || '<span class="badge bg-secondary">Indefinido</span>'
}

function showAlert(message, type = "info") {
  const alertDiv = document.createElement("div")
  alertDiv.className = `alert alert-${type} alert-dismissible fade show`
  alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `

  document.querySelector("main").insertBefore(alertDiv, document.querySelector("main").firstChild)

  // Auto-remover após 5 segundos
  setTimeout(() => {
    if (alertDiv.parentNode) {
      alertDiv.remove()
    }
  }, 5000)
}
