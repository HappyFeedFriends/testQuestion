<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    </head>
    <script>

        function OnSubmit(){
            const data = {
                value : document.querySelector('.serialNumber').value,
                mask : document.querySelector('.mask-value').value,
            }
            fetch('api/checkedPattern',{
                method : 'POST',
                headers : {
                    'Content-Type' : 'application/json',
                },
                body : JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {

                const container = document.querySelector('.results')
                container.innerHTML = ''

                if (res.error){
                    const htmlStr = `<div class="alert alert-danger" role="alert">${res.error}</div>`
                    const node = document.createElement('div')
                    node.innerHTML = htmlStr
                    container.appendChild(node)
                    return;
                }

                res.forEach(el => {
                    const htmlStr = `<div class="alert alert-${(el.isValid && !el.isHaveInDatabase) ? 'success' : 'danger'}" role="alert">${el.value} - ${el.isHaveInDatabase
                        ? 'this mask have in database'
                        : !el.isValid
                            ? 'Mask format not valid!'
                            : 'Mask added in database!'}</div>`
                    const node = document.createElement('div')
                    node.innerHTML = htmlStr
                    container.appendChild(node)
                })

            })

            return false;
        }

    </script>
    <body>
        <div class="d-flex flex-row" style="padding: 15px;">
            <div style="width:45vw; margin-right:35px;">
                <div class="col">
                    <span>Тип оборудования: </span>
                    <select onchange="document.querySelector('.mask-value').value = this.value;" class="form-control form-control-lg">
                        @foreach ($types as $typeData)
                            <option value="{{$typeData->mask}}">{{$typeData->name}}</option>
                        @endforeach
                    </select>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">Маска SN: </span>
                        </div>
                        <input type="text" readonly value="{{$types[0]->mask}}" class="form-control mask-value" aria-describedby="basic-addon3">
                    </div>
                </div>
                <form style="margin-top:10px;" onsubmit="return OnSubmit(this)">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Серийные номера: </span>
                        </div>
                        <textarea class="form-control serialNumber" aria-label="With textarea"></textarea>
                    </div>
                    <button class="btn-success btn" type="submit">Отправить</button>
                </form>

                <div class="flex-col">
                    <span>РЕЗУЛЬТАТЫ:</span>
                    <div class="flex-col results">

                    </div>

                </div>
            </div>

            <div class="col">
                <span>
                Где<br>

                N – цифра от 0 до 9,<br>

                A – прописная буква латинского алфавита,<br>

                a – строчная буква латинского алфавита,<br>

                X – прописная буква латинского алфавита либо цифра от 0 до 9,<br>

                Z –символ из списка: “-“, “_”, “@”.<br>
                </span>
            </div>
        </div>
    </body>
</html>
